<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->boolean('spouse_consent_thumbprint')->default(false)->after('spouse_consent');
            $table->boolean('business_has_license')->default(false)->after('business_capital');
            $table->string('business_license_number')->nullable()->after('business_has_license');
            $table->decimal('average_monthly_profit', 15, 2)->nullable()->after('monthly_revenue');
            $table->text('products_services')->nullable()->after('average_monthly_profit');
            $table->text('loan_main_purpose')->nullable()->after('loan_purpose');
            $table->date('repayment_start_date')->nullable()->after('repayment_frequency');
            $table->json('group_member_signatories')->nullable()->after('group_members_list');
            $table->json('group_leadership_acknowledgements')->nullable()->after('group_member_signatories');

            $table->index(['loan_product', 'status'], 'borrowers_product_status_idx');
            $table->index(['branch', 'registration_date'], 'borrowers_branch_registration_idx');
            $table->index('group_name', 'borrowers_group_name_idx');
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropIndex('borrowers_product_status_idx');
            $table->dropIndex('borrowers_branch_registration_idx');
            $table->dropIndex('borrowers_group_name_idx');
            $table->dropColumn([
                'spouse_consent_thumbprint',
                'business_has_license',
                'business_license_number',
                'average_monthly_profit',
                'products_services',
                'loan_main_purpose',
                'repayment_start_date',
                'group_member_signatories',
                'group_leadership_acknowledgements',
            ]);
        });
    }
};
