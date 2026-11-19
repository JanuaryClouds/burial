<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('superadmin_mfa_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->text('secret')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->uuid('version');
            $table->bigInteger('last_used_step')->nullable();
            $table->json('recovery_hashes')->nullable();
            $table->timestamps();
        });
        Schema::table('personal_access_tokens', fn (Blueprint $table) => $table->uuid('mfa_version')->nullable());
    }

    public function down(): void
    {
        Schema::table('personal_access_tokens', fn (Blueprint $table) => $table->dropColumn('mfa_version'));
        Schema::dropIfExists('superadmin_mfa_credentials');
    }
};
