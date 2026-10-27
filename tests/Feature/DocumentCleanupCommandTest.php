<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class DocumentCleanupCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'document_cleanup_testing',
            'database.connections.document_cleanup_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        ]);
        Schema::create('document_templates', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('path');
        });
        Storage::fake('document_templates');
    }

    protected function tearDown(): void
    {
        DB::purge('document_cleanup_testing');
        parent::tearDown();
    }

    private function file(string $path, int $ageHours = 48): void
    {
        $disk = Storage::disk('document_templates');
        $disk->put($path, 'sample document');
        touch($disk->path($path), now()->subHours($ageHours)->getTimestamp());
        clearstatcache(true, $disk->path($path));
    }

    private function reference(string $path): void
    {
        DB::table('document_templates')->insert(['uuid' => (string) Str::uuid(), 'path' => $path]);
    }

    public function test_dry_run_lists_orphans_without_deleting_files_or_records(): void
    {
        $this->file('templates/orphan.docx');
        $this->file('generated/abandoned.docx');
        $this->file('templates/referenced.docx');
        $this->reference('templates/referenced.docx');
        $this->artisan('documents:cleanup --dry-run')
            ->expectsOutput('Would delete: templates/orphan.docx')
            ->expectsOutput('Would delete: generated/abandoned.docx')
            ->expectsOutput('Preview: 2 eligible file(s). No files were deleted.')
            ->assertSuccessful();
        $this->assertCount(3, Storage::disk('document_templates')->allFiles());
        $this->assertSame(1, DB::table('document_templates')->count());
    }

    public function test_cleanup_preserves_references_recent_files_and_unrelated_paths(): void
    {
        foreach (['templates/orphan.docx', 'generated/abandoned.docx', 'templates/referenced.docx', 'templates/notes.txt', 'outside.docx', 'other/document.docx'] as $path) {
            $this->file($path);
        }
        $this->file('generated/in-progress.docx', 1);
        $this->file('templates/uploading.docx', 1);
        $this->reference('templates/referenced.docx');
        $this->reference('templates/missing.docx');
        $this->artisan('documents:cleanup')->expectsOutput('Deleted 2 orphaned document file(s).')->assertSuccessful();
        $disk = Storage::disk('document_templates');
        $disk->assertMissing(['templates/orphan.docx', 'generated/abandoned.docx']);
        $disk->assertExists(['templates/referenced.docx', 'templates/notes.txt', 'outside.docx', 'other/document.docx', 'generated/in-progress.docx', 'templates/uploading.docx']);
        $this->assertSame(2, DB::table('document_templates')->count());
        $this->artisan('documents:cleanup')->expectsOutput('Deleted 0 orphaned document file(s).')->assertSuccessful();
    }

    public function test_zero_hours_allows_immediate_testing_cleanup_but_preserves_references(): void
    {
        $this->file('templates/fresh-orphan.docx', 0);
        $this->file('generated/fresh-output.docx', 0);
        $this->file('templates/referenced.docx', 0);
        $this->reference('templates/referenced.docx');
        $this->artisan('documents:cleanup --hours=0')->expectsOutput('Deleted 2 orphaned document file(s).')->assertSuccessful();
        Storage::disk('document_templates')->assertExists('templates/referenced.docx');
    }

    public function test_custom_age_threshold_is_respected(): void
    {
        $this->file('templates/two-days.docx', 48);
        $this->file('generated/four-days.docx', 96);
        $this->artisan('documents:cleanup --hours=72')->expectsOutput('Deleted 1 orphaned document file(s).')->assertSuccessful();
        Storage::disk('document_templates')->assertExists('templates/two-days.docx');
        Storage::disk('document_templates')->assertMissing('generated/four-days.docx');
    }

    public function test_invalid_age_is_rejected_without_deleting_files(): void
    {
        $this->file('templates/orphan.docx');
        foreach (['-1', 'abc', '1.5', '876001'] as $hours) {
            $this->artisan('documents:cleanup', ['--hours' => $hours])->assertExitCode(2);
        }
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
    }

    public function test_database_failure_prevents_even_generated_file_deletion(): void
    {
        $this->file('templates/orphan.docx');
        $this->file('generated/abandoned.docx');
        Schema::drop('document_templates');
        $this->artisan('documents:cleanup')
            ->expectsOutput('Unable to read document references or storage. No files were deleted.')
            ->assertFailed();
        Storage::disk('document_templates')->assertExists(['templates/orphan.docx', 'generated/abandoned.docx']);
    }

    public function test_reference_created_during_scan_is_rechecked_before_deletion(): void
    {
        $disk = \Mockery::mock();
        $disk->shouldReceive('allFiles')->with('templates')->once()->andReturn(['templates/new-reference.docx']);
        $disk->shouldReceive('allFiles')->with('generated')->once()->andReturn([]);
        $disk->shouldReceive('exists')->once()->andReturn(true);
        $disk->shouldReceive('lastModified')->once()->andReturnUsing(function () {
            $this->reference('templates/new-reference.docx');

            return now()->subDays(2)->getTimestamp();
        });
        $disk->shouldNotReceive('delete');
        Storage::shouldReceive('disk')->with('document_templates')->once()->andReturn($disk);
        $this->artisan('documents:cleanup')->expectsOutput('Deleted 0 orphaned document file(s).')->assertSuccessful();
    }

    public function test_deletion_failure_returns_failure_status(): void
    {
        $disk = \Mockery::mock();
        $disk->shouldReceive('allFiles')->with('templates')->once()->andReturn(['templates/orphan.docx']);
        $disk->shouldReceive('allFiles')->with('generated')->once()->andReturn([]);
        $disk->shouldReceive('exists')->once()->andReturn(true);
        $disk->shouldReceive('lastModified')->once()->andReturn(now()->subDays(2)->getTimestamp());
        $disk->shouldReceive('delete')->with('templates/orphan.docx')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('document_templates')->once()->andReturn($disk);
        $this->artisan('documents:cleanup')->expectsOutput('Unable to check or delete: templates/orphan.docx')->assertFailed();
    }
}
