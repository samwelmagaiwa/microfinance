<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('photo_path');
            $table->string('residence_type_other')->nullable()->after('residence_type');
            $table->string('spouse_signature_name')->nullable()->after('spouse_consent');
            $table->date('spouse_signature_date')->nullable()->after('spouse_signature_name');
            $table->date('borrower_oath_date')->nullable()->after('borrower_oath');
            $table->boolean('borrower_oath_thumbprint')->default(false)->after('borrower_oath_date');
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn([
                'attachments',
                'residence_type_other',
                'spouse_signature_name',
                'spouse_signature_date',
                'borrower_oath_date',
                'borrower_oath_thumbprint',
            ]);
        });
    }
};
