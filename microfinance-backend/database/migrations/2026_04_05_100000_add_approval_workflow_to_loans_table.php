<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Approval workflow status
            $table->enum('approval_status', [
                'pending_loan_officer',
                'pending_loan_manager', 
                'pending_general_manager',
                'pending_managing_director',
                'approved',
                'rejected'
            ])->default('pending_loan_officer')->after('status');

            // Current step (for tracking)
            $table->string('current_approval_step')->default('loan_officer')->after('approval_status');

            // Loan Officer Approval
            $table->unsignedBigInteger('loan_officer_id')->nullable()->after('current_approval_step');
            $table->string('loan_officer_signature_id')->nullable()->after('loan_officer_id');
            $table->timestamp('loan_officer_approved_at')->nullable()->after('loan_officer_signature_id');
            $table->string('loan_officer_hash')->nullable()->after('loan_officer_approved_at');

            // Loan Manager Approval
            $table->unsignedBigInteger('loan_manager_id')->nullable()->after('loan_officer_hash');
            $table->string('loan_manager_signature_id')->nullable()->after('loan_manager_id');
            $table->timestamp('loan_manager_approved_at')->nullable()->after('loan_manager_signature_id');
            $table->string('loan_manager_hash')->nullable()->after('loan_manager_approved_at');

            // General Manager Approval
            $table->unsignedBigInteger('general_manager_id')->nullable()->after('loan_manager_hash');
            $table->string('general_manager_signature_id')->nullable()->after('general_manager_id');
            $table->timestamp('general_manager_approved_at')->nullable()->after('general_manager_signature_id');
            $table->string('general_manager_hash')->nullable()->after('general_manager_approved_at');

            // Managing Director Approval
            $table->unsignedBigInteger('managing_director_id')->nullable()->after('general_manager_hash');
            $table->string('managing_director_signature_id')->nullable()->after('managing_director_id');
            $table->timestamp('managing_director_approved_at')->nullable()->after('managing_director_signature_id');
            $table->string('managing_director_hash')->nullable()->after('managing_director_approved_at');

            // Rejection info
            $table->text('rejection_reason')->nullable()->after('managing_director_hash');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejection_reason');

            // Document hash for integrity verification
            $table->string('document_hash')->nullable()->after('rejected_by');
            $table->timestamp('hash_generated_at')->nullable()->after('document_hash');
        });

        // Add foreign keys
        Schema::table('loans', function (Blueprint $table) {
            $table->foreign('loan_officer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('loan_manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('general_manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('managing_director_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $columns = [
                'approval_status', 'current_approval_step',
                'loan_officer_id', 'loan_officer_signature_id', 'loan_officer_approved_at', 'loan_officer_hash',
                'loan_manager_id', 'loan_manager_signature_id', 'loan_manager_approved_at', 'loan_manager_hash',
                'general_manager_id', 'general_manager_signature_id', 'general_manager_approved_at', 'general_manager_hash',
                'managing_director_id', 'managing_director_signature_id', 'managing_director_approved_at', 'managing_director_hash',
                'rejection_reason', 'rejected_by', 'document_hash', 'hash_generated_at'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('loans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
