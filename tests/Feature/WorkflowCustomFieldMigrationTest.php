<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class WorkflowCustomFieldMigrationTest extends TestCase
{
    public function test_help_text_migration_defaults_existing_fields_to_null(): void
    {
        $uuid = $this->definition();
        $migration = require database_path('migrations/custom_fields/2026_10_26_100000_add_help_text_to_custom_fields.php');
        $migration->up();
        $this->assertNull(DB::table('custom_fields')->where('uuid', $uuid)->value('help_text'));
        DB::table('custom_fields')->where('uuid', $uuid)->update(['help_text' => 'Copy the printed reference.']);
        $this->assertSame('Copy the printed reference.', DB::table('custom_fields')->where('uuid', $uuid)->value('help_text'));
        $migration->down();
        $this->assertFalse(Schema::hasColumn('custom_fields', 'help_text'));
        $this->assertSame(1, DB::table('custom_fields')->count());
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'custom_fields_testing', 'database.connections.custom_fields_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ]]);
        foreach (['2026_07_10_400000_create_custom_fields_table.php', '2026_07_10_400001_create_custom_field_values_table.php'] as $file) {
            (require database_path('migrations/custom_fields/'.$file))->up();
        }
    }

    protected function tearDown(): void
    {
        DB::purge('custom_fields_testing');
        parent::tearDown();
    }

    private function migration()
    {
        return require database_path('migrations/custom_fields/2026_10_25_100000_prepare_versioned_stage_custom_fields.php');
    }

    private function definition(string $name = 'reference'): string
    {
        $uuid = (string) Str::uuid();
        DB::table('custom_fields')->insert(['uuid' => $uuid, 'fieldable_type' => 'LegacyOwner', 'fieldable_id' => 123,
            'name' => $name, 'label' => 'Reference', 'input_type' => 'text', 'required' => true, 'order_no' => 1]);

        return $uuid;
    }

    public function test_migration_preserves_legacy_data_and_accepts_uuid_owners(): void
    {
        $field = $this->definition();
        $value = (string) Str::uuid();
        DB::table('custom_field_values')->insert(['uuid' => $value, 'custom_field_uuid' => $field,
            'valuable_type' => 'LegacyLog', 'valuable_id' => 456, 'value' => '0000123']);
        $this->migration()->up();
        $this->assertSame('123', (string) DB::table('custom_fields')->value('fieldable_id'));
        $this->assertSame('456', (string) DB::table('custom_field_values')->value('valuable_id'));
        $this->assertSame('0000123', DB::table('custom_field_values')->value('value'));
        $owner = (string) Str::uuid();
        DB::table('custom_fields')->where('uuid', $field)->update(['fieldable_id' => $owner]);
        DB::table('custom_field_values')->where('uuid', $value)->update(['valuable_id' => $owner, 'value' => str_repeat('x', 1000)]);
        $this->assertSame($owner, DB::table('custom_fields')->value('fieldable_id'));
        $this->assertSame(1000, strlen(DB::table('custom_field_values')->value('value')));
        $this->expectException(QueryException::class);
        DB::table('custom_fields')->where('uuid', $field)->delete();
    }

    public function test_duplicate_definitions_fail_before_any_schema_changes(): void
    {
        $this->definition();
        $this->definition();
        try {
            $this->migration()->up();
            $this->fail('Duplicate names were accepted.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Duplicate records in custom_fields', $exception->getMessage());
        }
        $this->assertSame(2, DB::table('custom_fields')->count());
        $this->assertSame('integer', Schema::getColumnType('custom_fields', 'fieldable_id'));
    }

    public function test_duplicate_values_fail_before_any_schema_changes(): void
    {
        $field = $this->definition();
        foreach (range(1, 2) as $index) {
            DB::table('custom_field_values')->insert(['uuid' => (string) Str::uuid(), 'custom_field_uuid' => $field,
                'valuable_type' => 'LegacyLog', 'valuable_id' => 456, 'value' => 'value '.$index]);
        }
        $this->expectExceptionMessage('Duplicate records in custom_field_values');
        $this->migration()->up();
    }

    public function test_unique_definition_constraint_is_enforced(): void
    {
        $this->migration()->up();
        $this->definition();
        $this->expectException(QueryException::class);
        $this->definition();
    }

    public function test_unique_recorded_value_constraint_is_enforced(): void
    {
        $this->migration()->up();
        $field = $this->definition();
        DB::table('custom_field_values')->insert(['uuid' => (string) Str::uuid(), 'custom_field_uuid' => $field,
            'valuable_type' => 'LegacyLog', 'valuable_id' => (string) Str::uuid(), 'value' => 'one']);
        $duplicate = (array) DB::table('custom_field_values')->first();
        $duplicate['uuid'] = (string) Str::uuid();
        $this->expectException(QueryException::class);
        DB::table('custom_field_values')->insert($duplicate);
    }
}
