<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->index(['status', 'registration_date'], 'borrowers_status_registration_idx');
            $table->index(['reviewed_by_loan_manager_id', 'loan_manager_reviewed_at'], 'borrowers_lm_review_idx');
            $table->index(['reviewed_by_gm_id', 'gm_reviewed_at'], 'borrowers_gm_review_idx');
            $table->index(['reviewed_by_md_id', 'md_reviewed_at'], 'borrowers_md_review_idx');
            $table->index(['rejected_by_id', 'rejected_at'], 'borrowers_rejected_idx');
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropIndex('borrowers_status_registration_idx');
            $table->dropIndex('borrowers_lm_review_idx');
            $table->dropIndex('borrowers_gm_review_idx');
            $table->dropIndex('borrowers_md_review_idx');
            $table->dropIndex('borrowers_rejected_idx');
        });
    }
};
