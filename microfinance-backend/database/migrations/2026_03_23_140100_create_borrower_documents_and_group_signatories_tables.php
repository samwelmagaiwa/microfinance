<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrower_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrower_id')->constrained()->cascadeOnDelete();
            $table->string('document_type', 100);
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_required')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['borrower_id', 'document_type'], 'borrower_documents_borrower_type_idx');
        });

        Schema::create('group_member_signatories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrower_id')->constrained()->cascadeOnDelete();
            $table->string('category', 50);
            $table->string('role', 50)->nullable();
            $table->unsignedTinyInteger('sequence')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('signature_name')->nullable();
            $table->date('signed_at')->nullable();
            $table->boolean('thumbprint_confirmed')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['borrower_id', 'category'], 'group_signatories_borrower_category_idx');
            $table->index(['borrower_id', 'sequence'], 'group_signatories_borrower_sequence_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_member_signatories');
        Schema::dropIfExists('borrower_documents');
    }
};
