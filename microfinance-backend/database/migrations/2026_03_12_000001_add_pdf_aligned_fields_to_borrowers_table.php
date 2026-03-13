<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {

            // ── Section 1: Personal extras ────────────────────────────
            $table->string('id_issued_at')->nullable()->after('nida_number');         // Mahali kilipotolewa
            $table->string('residence_description')->nullable()->after('house_number'); // Jina maarufu
            $table->integer('children_count')->default(0)->after('dependents');       // Idadi ya watoto
            $table->string('spouse_name')->nullable()->after('children_count');        // Jina la mke/mume
            $table->string('spouse_phone')->nullable()->after('spouse_name');
            $table->string('spouse_workplace')->nullable()->after('spouse_phone');

            // ── Section 1.1: Spouse detail ────────────────────────────
            $table->string('spouse_full_name')->nullable();
            $table->date('spouse_dob')->nullable();
            $table->string('spouse_id_number')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->string('spouse_region')->nullable();
            $table->string('spouse_district')->nullable();
            $table->string('spouse_village')->nullable();
            $table->string('spouse_work_place')->nullable();
            $table->string('spouse_employer')->nullable();
            $table->string('spouse_employer_phone')->nullable();
            $table->decimal('spouse_monthly_income', 15, 2)->default(0);
            $table->boolean('spouse_consent')->default(false);

            // ── Section 2: Employment extras ─────────────────────────
            $table->string('employee_title')->nullable();           // Cheo
            $table->string('tenure_years')->nullable();             // Muda kazini
            $table->string('contract_type')->nullable();            // Kudumu / wa Muda
            $table->string('contract_duration')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->string('salary_payment_method')->nullable();    // Bank / Mobile / Cash
            $table->decimal('monthly_repayment_capacity', 15, 2)->default(0);
            $table->decimal('other_income', 15, 2)->default(0);
            $table->string('other_income_source')->nullable();

            // ── Financial extras ──────────────────────────────────────
            $table->decimal('monthly_expenses', 15, 2)->default(0);
            $table->decimal('asset_value', 15, 2)->default(0);      // Thamani halisi ya mali
            $table->decimal('other_income_financial', 15, 2)->default(0);

            // ── Section 3: Loan extras ────────────────────────────────
            $table->string('repayment_method')->nullable();         // kukatwa mshahara / kila mwezi
            $table->boolean('loan_purpose_biashara')->default(false);
            $table->boolean('loan_purpose_kilimo')->default(false);
            $table->boolean('loan_purpose_ada')->default(false);
            $table->boolean('loan_purpose_ujenzi')->default(false);
            $table->boolean('loan_purpose_ukarabati')->default(false);
            $table->boolean('loan_purpose_hospitali')->default(false);
            $table->boolean('loan_purpose_nyingine')->default(false);
            $table->string('loan_purpose_other')->nullable();
            $table->decimal('repayment_capacity', 15, 2)->default(0);

            // ── Guarantor extras (stored as JSON already, but also individual) ─
            // (guarantor1 and guarantor2 are already JSON — no schema change needed)

            // ── Section 7: Internal use extras ────────────────────────
            $table->string('loan_number')->nullable();
            $table->string('loan_officer_name')->nullable();
            $table->date('registration_date')->nullable();
            $table->text('loan_manager_remarks')->nullable();
            $table->text('gm_remarks')->nullable();
            $table->string('board_decision')->nullable();            // Approved / Rejected / Deferred
            $table->boolean('borrower_oath')->default(false);        // Kiapo cha mkopaji
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn([
                'id_issued_at', 'residence_description', 'children_count',
                'spouse_name', 'spouse_phone', 'spouse_workplace',
                'spouse_full_name', 'spouse_dob', 'spouse_id_number',
                'spouse_occupation', 'spouse_region', 'spouse_district',
                'spouse_village', 'spouse_work_place', 'spouse_employer',
                'spouse_employer_phone', 'spouse_monthly_income', 'spouse_consent',
                'employee_title', 'tenure_years', 'contract_type',
                'contract_duration', 'contract_start_date', 'salary_payment_method',
                'monthly_repayment_capacity', 'other_income', 'other_income_source',
                'monthly_expenses', 'asset_value', 'other_income_financial',
                'repayment_method',
                'loan_purpose_biashara', 'loan_purpose_kilimo', 'loan_purpose_ada',
                'loan_purpose_ujenzi', 'loan_purpose_ukarabati', 'loan_purpose_hospitali',
                'loan_purpose_nyingine', 'loan_purpose_other', 'repayment_capacity',
                'loan_number', 'loan_officer_name', 'registration_date',
                'loan_manager_remarks', 'gm_remarks', 'board_decision', 'borrower_oath',
            ]);
        });
    }
};
