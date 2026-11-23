<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('walk_in_intakes', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignId('encoded_by')->constrained('users')->restrictOnDelete();
            $table->longText('data');
            $table->longText('documents')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->uuid('application_uuid')->nullable()->unique();
            $table->foreign('application_uuid')->references('uuid')->on('applications')->restrictOnDelete();
            $table->string('claim_code_hash', 64)->nullable()->unique();
            $table->timestamp('claim_expires_at')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->foreignId('claimed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->uuid('walk_in_intake_uuid')->nullable()->unique();
            $table->foreign('walk_in_intake_uuid')->references('uuid')->on('walk_in_intakes')->restrictOnDelete();
            $table->text('identity_snapshot')->nullable();
        });
        DB::table('permissions')->insertOrIgnore(['name' => 'Create Walk-in Intake', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('walk_in_intakes')->exists() || DB::table('clients')->whereNull('user_id')->exists()) {
            throw new RuntimeException('Preserve and migrate existing walk-in intake data before rolling back this feature.');
        }
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['walk_in_intake_uuid']);
            $table->dropColumn(['walk_in_intake_uuid', 'identity_snapshot']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
        Schema::dropIfExists('walk_in_intakes');
    }
};
