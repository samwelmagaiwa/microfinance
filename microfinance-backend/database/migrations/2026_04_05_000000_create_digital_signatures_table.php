<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->morphs('signable');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('signature_id')->nullable()->unique();
            $table->text('signature_data');
            $table->string('document_hash')->nullable();
            $table->string('encryption_key', 64)->nullable();
            $table->string('signed_by_name')->nullable();
            $table->string('signed_by_role')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->enum('status', ['approved', 'rejected', 'pending'])->default('approved');
            $table->text('rejection_reason')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('hash')->nullable();
            $table->timestamps();

            $table->index('signature_id');
            $table->index('user_id');
            $table->index('signed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};
