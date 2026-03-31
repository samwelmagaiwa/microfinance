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
        Schema::table('loans', function (Blueprint $table) {
            $table->string('loan_number')->nullable()->after('borrower_id');
            $table->date('disbursed_at')->nullable()->after('status');
            $table->date('first_payment_date')->nullable()->after('disbursed_at');
            $table->decimal('monthly_payment', 15, 2)->nullable()->after('first_payment_date');
            $table->decimal('total_interest', 15, 2)->nullable()->after('monthly_payment');
            $table->decimal('total_payment', 15, 2)->nullable()->after('total_interest');
            $table->string('loan_product')->nullable()->after('total_payment');
            $table->string('repayment_method')->nullable()->after('loan_product');
            $table->string('repayment_frequency')->default('monthly')->after('repayment_method');
            $table->text('collateral_description')->nullable()->after('repayment_frequency');
            $table->string('guarantor1_name')->nullable()->after('collateral_description');
            $table->string('guarantor1_phone')->nullable()->after('guarantor1_name');
            $table->string('guarantor2_name')->nullable()->after('guarantor1_phone');
            $table->string('guarantor2_phone')->nullable()->after('guarantor2_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $columns = [
                'loan_number', 'disbursed_at', 'first_payment_date', 'monthly_payment',
                'total_interest', 'total_payment', 'loan_product', 'repayment_method',
                'repayment_frequency', 'collateral_description', 'guarantor1_name',
                'guarantor1_phone', 'guarantor2_name', 'guarantor2_phone'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('loans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
