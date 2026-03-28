<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            // Reviewer tracking
            if (!Schema::hasColumn('borrowers', 'reviewed_by_loan_manager_id')) {
                $table->foreignId('reviewed_by_loan_manager_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('borrowers', 'reviewed_by_gm_id')) {
                $table->foreignId('reviewed_by_gm_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('borrowers', 'reviewed_by_md_id')) {
                $table->foreignId('reviewed_by_md_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('borrowers', 'rejected_by_id')) {
                $table->foreignId('rejected_by_id')->nullable()->constrained('users')->nullOnDelete();
            }

            // Review timestamps
            if (!Schema::hasColumn('borrowers', 'loan_manager_reviewed_at')) {
                $table->timestamp('loan_manager_reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'gm_reviewed_at')) {
                $table->timestamp('gm_reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'md_reviewed_at')) {
                $table->timestamp('md_reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }

            // Review remarks
            if (!Schema::hasColumn('borrowers', 'loan_manager_remarks')) {
                $table->text('loan_manager_remarks')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'gm_remarks')) {
                $table->text('gm_remarks')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'md_remarks')) {
                $table->text('md_remarks')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by_loan_manager_id']);
            $table->dropForeign(['reviewed_by_gm_id']);
            $table->dropForeign(['reviewed_by_md_id']);
            $table->dropForeign(['rejected_by_id']);

            $table->dropColumn([
                'reviewed_by_loan_manager_id',
                'reviewed_by_gm_id',
                'reviewed_by_md_id',
                'rejected_by_id',
                'loan_manager_reviewed_at',
                'gm_reviewed_at',
                'md_reviewed_at',
                'rejected_at',
                'loan_manager_remarks',
                'gm_remarks',
                'md_remarks',
                'rejection_reason',
            ]);
        });
    }
};
