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
            $table->date('business_start_date')->nullable()->after('business_name');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
            $table->string('group_previously_loaned')->nullable()->after('group_bank_name');
            $table->string('group_years')->nullable()->after('group_previously_loaned');
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn(['business_start_date', 'contract_end_date', 'group_previously_loaned', 'group_years']);
        });
    }
};
