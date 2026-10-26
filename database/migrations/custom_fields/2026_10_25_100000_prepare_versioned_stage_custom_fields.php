<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Preflight before any DDL (MySQL cannot roll back these alterations).
        foreach ([
            ['custom_fields', ['fieldable_type', 'fieldable_id', 'name']],
            ['custom_field_values', ['valuable_type', 'valuable_id', 'custom_field_uuid']],
        ] as [$table, $columns]) {
            if (DB::table($table)->select($columns)->groupBy($columns)->havingRaw('count(*) > 1')->exists()) {
                throw new RuntimeException("Duplicate records in {$table}. Resolve duplicate owner/field keys before retrying this migration; no records were removed.");
            }
        }
        if (DB::table('custom_field_values as v')->leftJoin('custom_fields as f', 'f.uuid', '=', 'v.custom_field_uuid')
            ->whereNull('f.uuid')->exists()) {
            throw new RuntimeException('Orphaned custom_field_values exist. Repair their definition references before retrying.');
        }
        Schema::table('custom_field_values', fn (Blueprint $table) => $table->dropForeign(['custom_field_uuid']));
        Schema::table('custom_fields', function (Blueprint $table) {
            // Strings preserve legacy numeric owners while supporting stage UUIDs.
            $table->string('fieldable_id', 36)->change();
            $table->unique(['fieldable_type', 'fieldable_id', 'name'], 'custom_fields_owner_name_unique');
        });
        Schema::table('custom_field_values', function (Blueprint $table) {
            $table->string('valuable_id', 36)->change();
            $table->text('value')->change();
            $table->unique(['valuable_type', 'valuable_id', 'custom_field_uuid'], 'custom_values_owner_field_unique');
            $table->foreign('custom_field_uuid')->references('uuid')->on('custom_fields')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        // UUID owners and encrypted values cannot safely fit the original columns.
        throw new RuntimeException('This data-preserving migration requires a reviewed backup restore to roll back. UUID owners and encrypted values must not be truncated.');
    }
};
