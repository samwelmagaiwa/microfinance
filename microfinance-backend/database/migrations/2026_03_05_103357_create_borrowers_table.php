<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Registered by
            
            // Step 1: Profile & Identity
            $table->string('full_name');
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->string('id_type')->nullable();
            $table->string('nida_number')->unique();
            $table->date('id_expiry_date')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('phone');
            $table->string('alt_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('marital_status')->nullable();
            $table->integer('dependents')->default(0);
            $table->string('photo_path')->nullable();

            // Step 2: Address
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->string('village')->nullable();
            $table->string('house_number')->nullable();
            $table->string('residence_type')->nullable();
            $table->integer('years_at_address')->nullable();
            $table->string('postal_address')->nullable();

            // Step 2: Economic & Business
            $table->string('employment_status')->nullable();
            $table->string('employer_name')->nullable();
            $table->text('employer_address')->nullable();
            $table->string('employer_phone')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('monthly_salary', 15, 2)->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_type')->nullable();
            $table->string('business_location')->nullable();
            $table->integer('years_in_business')->nullable();
            $table->decimal('monthly_revenue', 15, 2)->nullable();

            // Step 2: Financial History
            $table->boolean('existing_loans')->default(false);
            $table->text('other_institutions')->nullable();
            $table->decimal('total_existing_amount', 15, 2)->nullable();
            $table->decimal('current_savings', 15, 2)->nullable();

            // Step 2: Banking
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('mobile_money_number')->nullable();

            // Step 2: Guarantors (JSON)
            $table->json('guarantor1')->nullable();
            $table->json('guarantor2')->nullable();

            // Step 2: Loan Request
            $table->string('loan_product')->nullable();
            $table->decimal('loan_amount', 15, 2)->nullable();
            $table->text('loan_purpose')->nullable();
            $table->integer('repayment_period')->nullable();
            $table->string('repayment_frequency')->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->decimal('mandatory_savings', 15, 2)->nullable();

            // Step 3/4: Internal & Status
            $table->string('borrower_account_number')->nullable();
            $table->string('branch')->nullable();
            $table->string('risk_assessment')->nullable();
            $table->string('status')->default('pending_loan_manager');
            $table->text('officer_remarks')->nullable();
            $table->boolean('officer_confirmed')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowers');
    }
};
