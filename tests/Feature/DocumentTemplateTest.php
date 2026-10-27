<?php

namespace Tests\Feature;

use App\Livewire\Application\Show;
use App\Livewire\FuneralAssistanceType\Templates;
use App\Models\Address;
use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\DocumentTemplate;
use App\Models\FuneralAssistanceType;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\DocumentPlaceholderRegistry;
use App\Services\DocumentTemplateProcessor;
use App\Services\DocumentTemplateService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;
use ZipArchive;

class DocumentTemplateTest extends TestCase
{
    private array $temporaryFiles = [];

    private FuneralAssistanceType $type;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'document_testing',
            'database.connections.document_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array',
            'app.key' => 'base64:'.base64_encode(str_repeat('d', 32)),
        ]);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('migrate', ['--database' => 'document_testing', '--force' => true]);
        foreach (['sexes', 'religions', 'relationships', 'mode_of_assistances'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        foreach (['Create Rejection', 'Create Referral', 'Create Closure', 'Create Interview', 'Create Assessment', 'Create Recommendation'] as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
        Storage::fake('document_templates');
        $this->admin = $this->user('Admin');
        $this->admin->assignRole(Role::findOrCreate('superadmin', 'web'));
        $this->actingAs($this->admin);
        $this->type = FuneralAssistanceType::create(['name' => 'Mortuary', 'description' => 'Mortuary assistance']);
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        Model::preventLazyLoading(! app()->isProduction());
        DB::purge('document_testing');
        parent::tearDown();
    }

    private function user(string $name): User
    {
        return User::create(['first_name' => $name, 'middle_name' => 'Santos', 'last_name' => 'Dela Cruz', 'email' => Str::uuid().'@example.test', 'password' => 'test-password', 'is_active' => true]);
    }

    private function path(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'burial-doc-');
        $this->temporaryFiles[] = $path;

        return $path;
    }

    private function docx(string $text = '${client.fullname}', bool $allParts = false): UploadedFile
    {
        $word = new PhpWord;
        $section = $word->addSection();
        $section->addText($text);
        if ($allParts) {
            $section->addHeader()->addText('Header ${client.fullname}');
            $section->addFooter()->addText('Footer ${beneficiary.fullname}');
            $section->addTable()->addRow()->addCell()->addText('Table ${recommendation.amount_extended}');
            $section->addText('${client.fullname}');
            $section->addText('${beneficiary.date_of_birth} ${beneficiary.date_of_death} ${client.suffix}');
        }
        $path = $this->path();
        IOFactory::createWriter($word, 'Word2007')->save($path);

        return UploadedFile::fake()->createWithContent('mortuary.docx', file_get_contents($path));
    }

    private function application(bool $recommended = true): Application
    {
        $client = Client::create(['user_id' => $this->user('José & <Ana>')->id, 'date_of_birth' => '1980-01-02', 'contact_number' => '09123456789']);
        Address::create(['addressable_type' => Client::class, 'addressable_id' => $client->uuid, 'house_number' => '1', 'street' => 'Main', 'region_code' => '13', 'municipality_code' => '137607', 'barangay_code' => '137607001', 'barangay_name' => 'Bagumbayan', 'municipality_name' => 'Taguig', 'region_name' => 'NCR']);
        $beneficiary = Beneficiary::create(['first_name' => 'María', 'last_name' => 'Cruz', 'sex_id' => 1, 'religion_id' => 1, 'date_of_birth' => '1960-02-03', 'date_of_death' => '2026-10-20', 'created_by' => $this->admin->id]);
        $application = Application::create(['client_uuid' => $client->uuid, 'beneficiary_uuid' => $beneficiary->uuid, 'relationship_id' => 1]);
        if ($recommended) {
            $recommendation = Recommendation::create(['application_uuid' => $application->uuid, 'claimant_assignment_uuid' => $application->current_claimant_assignment_uuid, 'funeral_assistance_type_uuid' => $this->type->uuid, 'recommended_by' => $this->admin->id, 'mode_of_assistance_id' => 1, 'amount_extended' => 12345.5]);
            $application->update(['current_recommendation_uuid' => $recommendation->uuid]);
        }

        return $application;
    }

    private function saveTemplate(string $text = '${client.fullname}'): DocumentTemplate
    {
        return app(DocumentTemplateService::class)->save($this->type, 'Mortuary Letter', $this->docx($text));
    }

    public function test_superadmin_can_add_replace_download_original_and_remove_templates(): void
    {
        Livewire::test(Templates::class, ['funeralAssistanceType' => $this->type])
            ->set('name', 'Mortuary Letter')->set('upload', $this->docx())->call('save')->assertHasNoErrors()->assertSee('Mortuary Letter');
        $template = $this->type->documentTemplates()->firstOrFail();
        $oldPath = $template->path;
        $this->get(route('funeral_assistance_types.templates.download', [$this->type, $template]))->assertOk()->assertDownload('mortuary-letter.docx');
        Livewire::test(Templates::class, ['funeralAssistanceType' => $this->type])
            ->call('replace', $template->uuid)->set('name', 'Updated Letter')->set('upload', $this->docx('${client.address}'))->call('save')->assertHasNoErrors();
        $template->refresh();
        $this->assertSame('Updated Letter', $template->name);
        Storage::disk('document_templates')->assertMissing($oldPath);
        Storage::disk('document_templates')->assertExists($template->path);
        Livewire::test(Templates::class, ['funeralAssistanceType' => $this->type])->call('remove', $template->uuid)->assertHasNoErrors();
        $this->assertNull($template->fresh());
        Storage::disk('document_templates')->assertMissing($template->path);
    }

    public function test_upload_rejects_unknown_keys_and_keeps_original_on_failed_replacement(): void
    {
        $template = $this->saveTemplate();
        Livewire::test(Templates::class, ['funeralAssistanceType' => $this->type])
            ->call('replace', $template->uuid)->set('upload', $this->docx('${client.fullnmae}'))->call('save')
            ->assertHasErrors('upload')->assertSee('client.fullnmae');
        $this->assertSame($template->path, $template->fresh()->path);
        $this->assertCount(1, Storage::disk('document_templates')->allFiles('templates'));
    }

    public function test_invalid_extensions_corrupt_packages_and_oversize_files_are_rejected(): void
    {
        $valid = $this->docx();
        $badZipPath = $this->path();
        $zip = new ZipArchive;
        $zip->open($badZipPath, ZipArchive::OVERWRITE);
        $zip->addFromString('hello.txt', 'not Word');
        $zip->close();
        foreach ([new UploadedFile($valid->getRealPath(), 'fake.zip', null, null, true), UploadedFile::fake()->createWithContent('broken.docx', 'broken'), new UploadedFile($badZipPath, 'fake.docx', null, null, true), UploadedFile::fake()->create('large.docx', 5121)] as $upload) {
            try {
                app(DocumentTemplateService::class)->save($this->type, 'Invalid', $upload);
                $this->fail('Invalid upload accepted.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('upload', $exception->errors());
            }
        }
        $this->assertSame(0, DocumentTemplate::count());
    }

    public function test_static_documents_are_accepted(): void
    {
        $this->saveTemplate('No variables');
        $this->assertSame(1, DocumentTemplate::count());
    }

    public function test_phpword_internal_temporary_copy_is_removed_after_inspection(): void
    {
        $upload = $this->docx();
        $processor = app(DocumentTemplateProcessor::class)->inspect($upload->getRealPath());
        $temporaryPath = $processor->getTempDocumentFilename();
        $this->assertFileExists($temporaryPath);
        unset($processor);
        $this->assertFileDoesNotExist($temporaryPath);
    }

    public function test_malformed_word_xml_is_rejected_even_with_a_valid_docx_zip(): void
    {
        $source = $this->path();
        $upload = $this->docx('${client.fullname}', true);
        copy($upload->getRealPath(), $source);
        $zip = new ZipArchive;
        $zip->open($source);
        $zip->addFromString('word/header1.xml', '<broken');
        $zip->close();
        $this->expectException(ValidationException::class);
        app(DocumentTemplateProcessor::class)->inspect($source);
    }

    public function test_printable_panel_refreshes_with_recommendation_and_never_generates_during_polling(): void
    {
        $application = $this->application(false);
        $template = $this->saveTemplate();
        $this->mock(DocumentTemplateProcessor::class, fn ($mock) => $mock->shouldNotReceive('generate'));
        $panel = Livewire::test(Show::class, ['application' => $application])
            ->assertSee('Printable Documents')->assertSee('Print GIS Form')->assertDontSee('Print Certificate');
        $run = Recommendation::create(['application_uuid' => $application->uuid, 'claimant_assignment_uuid' => $application->current_claimant_assignment_uuid, 'funeral_assistance_type_uuid' => $this->type->uuid, 'recommended_by' => $this->admin->id, 'mode_of_assistance_id' => 1]);
        $application->update(['current_recommendation_uuid' => $run->uuid]);
        $panel->dispatch('refreshRecommendation')->assertSee('Print Certificate')->assertSee('Mortuary Letter')
            ->call('refreshApplication')->assertSee('Mortuary Letter');
        $other = FuneralAssistanceType::create(['name' => 'Other', 'description' => 'Other']);
        $run->update(['funeral_assistance_type_uuid' => $other->uuid]);
        $panel->call('refreshApplication')->assertDontSee('Mortuary Letter')->assertSee('No accessory documents are configured');
    }

    public function test_staff_cannot_manage_templates_even_by_direct_service_call(): void
    {
        $template = $this->saveTemplate();
        $staff = $this->user('Staff');
        $staff->assignRole(Role::findOrCreate('staff', 'web'));
        $this->actingAs($staff);
        Livewire::test(Templates::class, ['funeralAssistanceType' => $this->type])->assertForbidden();
        $this->getJson(route('funeral_assistance_types.templates.download', [$this->type, $template]))->assertForbidden();
        try {
            app(DocumentTemplateService::class)->save($this->type, 'Unauthorized', $this->docx());
            $this->fail('Unauthorized service call accepted.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
        try {
            app(DocumentTemplateService::class)->remove($this->type, $template->uuid);
            $this->fail('Unauthorized removal accepted.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_cross_type_management_and_download_are_rejected(): void
    {
        $template = $this->saveTemplate();
        $other = FuneralAssistanceType::create(['name' => 'Other', 'description' => 'Other']);
        $this->get(route('funeral_assistance_types.templates.download', [$other, $template]))->assertNotFound();
        Livewire::test(Templates::class, ['funeralAssistanceType' => $other])->call('replace', $template->uuid)->assertNotFound();
        Livewire::test(Templates::class, ['funeralAssistanceType' => $other])->call('remove', $template->uuid)->assertNotFound();
    }

    public function test_real_docx_substitution_preserves_parts_and_escapes_xml_with_lazy_loading_disabled(): void
    {
        $application = $this->application();
        $upload = $this->docx('${client.fullname}', true);
        $source = $this->path();
        copy($upload->getRealPath(), $source);
        $zip = new ZipArchive;
        $zip->open($source);
        $body = $zip->getFromName('word/document.xml');
        $body = preg_replace('/\$\{client\.fullname\}/', '${client.</w:t></w:r><w:r><w:t>fullname}', $body, 1);
        $zip->addFromString('word/document.xml', $body);
        $zip->close();
        $application = Application::whereKey($application->uuid)->get()->first();
        $application->preventsLazyLoading = true;
        Model::preventLazyLoading(true);
        $destination = $this->path();
        app(DocumentTemplateProcessor::class)->generate($source, $destination, $application);
        $this->assertSame([], (new TemplateProcessor($destination))->getVariables());
        $zip->open($destination);
        foreach (['word/document.xml', 'word/header1.xml', 'word/footer1.xml'] as $part) {
            $document = new \DOMDocument;
            $this->assertTrue($document->loadXML($zip->getFromName($part)));
            $text = $document->textContent;
            if ($part === 'word/document.xml') {
                $this->assertStringContainsString('José & <Ana> S. Dela Cruz', $text);
                $this->assertStringContainsString('12,345.50', $text);
                $this->assertStringContainsString('February 3, 1960 October 20, 2026', $text);
                $this->assertSame(2, substr_count($text, 'José & <Ana> S. Dela Cruz'));
            } else {
                $this->assertStringContainsString($part === 'word/header1.xml' ? 'José & <Ana>' : 'María Cruz', $text);
            }
        }
        $zip->close();
    }

    public function test_registry_covers_all_keys_and_formats_blank_and_current_values(): void
    {
        $application = $this->application();
        $registry = app(DocumentPlaceholderRegistry::class);
        $values = $registry->values($application, array_keys($registry->definitions()));
        $this->assertCount(22, $values);
        $this->assertSame('', $values['client.suffix']);
        $this->assertSame('', $values['beneficiary.address']);
        $this->assertSame('January 2, 1980', $values['client.date_of_birth']);
        $this->assertSame('Mortuary', $values['recommendation.funeral_assistance_type']);
        $this->assertSame('1 Main, Bagumbayan, Taguig, NCR', $values['client.address']);
        $this->assertSame('12,345.50', $values['recommendation.amount_extended']);
    }

    public function test_downloads_use_latest_template_and_data_and_unique_temporary_files(): void
    {
        $application = $this->application();
        $template = $this->saveTemplate();
        $route = route('application.templates.download', [$application, $template]);
        $first = $this->get($route)->assertOk()->assertDownload();
        $firstPath = $first->baseResponse->getFile()->getPathname();
        $application->client->user->update(['first_name' => 'Changed']);
        app(DocumentTemplateService::class)->save($this->type, 'Mortuary Letter', $this->docx('Updated ${client.first_name}'), $template->uuid);
        $second = $this->get($route)->assertOk();
        $secondPath = $second->baseResponse->getFile()->getPathname();
        $this->assertNotSame($firstPath, $secondPath);
        $zip = new ZipArchive;
        $zip->open($secondPath);
        $this->assertStringContainsString('Updated Changed', strip_tags($zip->getFromName('word/document.xml')));
        $zip->close();
        foreach ([$first, $second] as $response) {
            ob_start();
            $response->baseResponse->sendContent();
            ob_end_clean();
        }
        $this->assertFileDoesNotExist($firstPath);
        $this->assertFileDoesNotExist($secondPath);
    }

    public function test_stale_recommendation_links_missing_files_and_removed_templates_return_404(): void
    {
        $application = $this->application();
        $template = $this->saveTemplate();
        $route = route('application.templates.download', [$application, $template]);
        $application->activeRecommendation->update(['funeral_assistance_type_uuid' => FuneralAssistanceType::create(['name' => 'Other', 'description' => 'Other'])->uuid]);
        $this->get($route)->assertNotFound();
        $application->activeRecommendation->update(['funeral_assistance_type_uuid' => $this->type->uuid]);
        Storage::disk('document_templates')->delete($template->path);
        $this->get($route)->assertNotFound();
        app(DocumentTemplateService::class)->remove($this->type, $template->uuid);
        $this->get($route)->assertNotFound();
        $this->get(route('application.templates.download', [$this->application(false), $template]))->assertNotFound();
    }

    public function test_download_access_requires_cswdo_or_superadmin_and_authentication(): void
    {
        $application = $this->application();
        $template = $this->saveTemplate();
        $route = route('application.templates.download', [$application, $template]);
        $this->actingAs($application->client->user)->getJson($route)->assertForbidden();
        $staff = $this->user('Staff');
        $staff->assignRole(Role::findOrCreate('staff', 'web'));
        $this->actingAs($staff)->getJson($route)->assertForbidden();
        $staff->assignRole(Role::findOrCreate('CSWDO', 'web'));
        $this->actingAs($staff)->get($route)->assertOk();
        auth()->logout();
        $this->get($route)->assertRedirect();
    }

    public function test_generation_failure_is_readable_cleans_partial_file_and_logs_no_values(): void
    {
        $application = $this->application();
        $template = $this->saveTemplate();
        Log::spy();
        $this->mock(DocumentTemplateProcessor::class, function ($mock) {
            $mock->shouldReceive('generate')->once()->andReturnUsing(function ($source, $destination) {
                file_put_contents($destination, 'partial');
                throw new \RuntimeException('Sensitive applicant value');
            });
        });
        $this->get(route('application.templates.download', [$application, $template]))->assertStatus(503);
        $this->assertSame([], Storage::disk('document_templates')->allFiles('generated'));
        Log::shouldHaveReceived('error')->once()->with('Accessory document generation failed.', ['template_uuid' => $template->uuid, 'exception_type' => \RuntimeException::class]);
    }

    public function test_failed_database_write_cleans_new_file_and_keeps_old_file(): void
    {
        $template = $this->saveTemplate();
        DocumentTemplate::updating(fn () => throw new \RuntimeException('Simulated database failure'));
        try {
            app(DocumentTemplateService::class)->save($this->type, 'Replacement', $this->docx(), $template->uuid);
            $this->fail('Expected database failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated database failure', $exception->getMessage());
        } finally {
            DocumentTemplate::flushEventListeners();
        }
        Storage::disk('document_templates')->assertExists($template->path);
        $this->assertCount(1, Storage::disk('document_templates')->allFiles('templates'));
        $this->assertSame($template->path, $template->fresh()->path);
    }

    public function test_storage_failure_does_not_publish_a_template(): void
    {
        $upload = $this->docx();
        $disk = \Mockery::mock();
        $disk->shouldReceive('put')->once()->andReturn(false);
        $disk->shouldReceive('delete')->once()->andReturn(true);
        Storage::shouldReceive('disk')->with('document_templates')->andReturn($disk);
        try {
            app(DocumentTemplateService::class)->save($this->type, 'New template', $upload);
            $this->fail('Expected storage failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Unable to store document template.', $exception->getMessage());
        }
        $this->assertSame(0, DocumentTemplate::count());
    }
}
