<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            // Employment Loan specific fields
            if (!Schema::hasColumn('borrowers', 'net_salary')) {
                $table->decimal('net_salary', 15, 2)->nullable()->after('monthly_salary');
            }
            if (!Schema::hasColumn('borrowers', 'moving_reason')) {
                $table->text('moving_reason')->nullable()->after('previous_business_location');
            }
            if (!Schema::hasColumn('borrowers', 'calculated_capacity')) {
                $table->decimal('calculated_capacity', 15, 2)->nullable()->after('repayment_capacity');
            }

            // Local Government verification fields
            if (!Schema::hasColumn('borrowers', 'local_govt_verification_date')) {
                $table->date('local_govt_verification_date')->nullable()->after('local_govt_chairman_title');
            }
            if (!Schema::hasColumn('borrowers', 'local_govt_stamp')) {
                $table->boolean('local_govt_stamp')->default(false)->after('local_govt_verification_date');
            }

            // Risk assessment checkboxes
            if (!Schema::hasColumn('borrowers', 'risk_high')) {
                $table->boolean('risk_high')->default(false)->after('risk_description');
            }
            if (!Schema::hasColumn('borrowers', 'risk_medium')) {
                $table->boolean('risk_medium')->default(false)->after('risk_high');
            }
            if (!Schema::hasColumn('borrowers', 'risk_low')) {
                $table->boolean('risk_low')->default(false)->after('risk_medium');
            }
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $columns = [
                'net_salary',
                'moving_reason',
                'calculated_capacity',
                'local_govt_verification_date',
                'local_govt_stamp',
                'risk_high',
                'risk_medium',
                'risk_low',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('borrowers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
