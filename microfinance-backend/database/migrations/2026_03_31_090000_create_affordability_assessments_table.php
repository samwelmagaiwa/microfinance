<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affordability_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrower_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Income
            $table->decimal('salary', 15, 2)->default(0);
            $table->decimal('business_income', 15, 2)->default(0);
            $table->decimal('other_income', 15, 2)->default(0);
            $table->decimal('total_income', 15, 2)->default(0);
            
            // Expenses
            $table->decimal('rent', 15, 2)->default(0);
            $table->decimal('food', 15, 2)->default(0);
            $table->decimal('transport', 15, 2)->default(0);
            $table->decimal('utilities', 15, 2)->default(0);
            $table->decimal('school_fees', 15, 2)->default(0);
            $table->decimal('existing_loan_repayments', 15, 2)->default(0);
            $table->decimal('other_expenses', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
            
            // Assessment Results
            $table->decimal('net_disposable_income', 15, 2)->default(0);
            $table->decimal('max_affordable_installment', 15, 2)->default(0);
            $table->decimal('affordability_threshold_percent', 5, 2)->default(40);
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->text('risk_message')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affordability_assessments');
    }
};
