<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email', 254);
            // A nullable unique key permits historical invitations after revocation/acceptance.
            $table->string('pending_email', 254)->nullable()->unique();
            $table->json('roles');
            $table->unsignedBigInteger('inviter_id');
            $table->string('token_hash', 64)->unique();
            $table->text('token')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
        Schema::table('mail_deliveries', function (Blueprint $table) {
            $table->foreignId('user_invitation_id')->nullable()->constrained('user_invitations');
        });
    }

    public function down(): void
    {
        Schema::table('mail_deliveries', fn (Blueprint $table) => $table->dropConstrainedForeignId('user_invitation_id'));
        Schema::dropIfExists('user_invitations');
    }
};
