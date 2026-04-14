<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            // Using text instead of string to avoid row size limits
            if (!Schema::hasColumn('borrowers', 'loan_officer_hash')) {
                $table->text('loan_officer_hash')->nullable()->after('officer_remarks');
            }
            if (!Schema::hasColumn('borrowers', 'loan_manager_hash')) {
                $table->text('loan_manager_hash')->nullable()->after('loan_manager_remarks');
            }
            if (!Schema::hasColumn('borrowers', 'gm_hash')) {
                $table->text('gm_hash')->nullable()->after('gm_remarks');
            }
            if (!Schema::hasColumn('borrowers', 'md_hash')) {
                $table->text('md_hash')->nullable()->after('md_remarks');
            }

            // Timestamps
            if (!Schema::hasColumn('borrowers', 'loan_officer_signed_at')) {
                $table->timestamp('loan_officer_signed_at')->nullable()->after('loan_officer_hash');
            }
            if (!Schema::hasColumn('borrowers', 'loan_manager_signed_at')) {
                $table->timestamp('loan_manager_signed_at')->nullable()->after('loan_manager_hash');
            }
            if (!Schema::hasColumn('borrowers', 'gm_signed_at')) {
                $table->timestamp('gm_signed_at')->nullable()->after('gm_hash');
            }
            if (!Schema::hasColumn('borrowers', 'md_signed_at')) {
                $table->timestamp('md_signed_at')->nullable()->after('md_hash');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn([
                'loan_officer_hash', 'loan_officer_signed_at',
                'loan_manager_hash', 'loan_manager_signed_at',
                'gm_hash', 'gm_signed_at',
                'md_hash', 'md_signed_at'
            ]);
        });
    }
};
