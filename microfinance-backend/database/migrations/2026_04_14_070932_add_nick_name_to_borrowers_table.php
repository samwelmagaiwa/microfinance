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
            // First, convert high-density varchar columns to TEXT to resolve "Row size too large" (1118)
            $table->text('residence_description')->nullable()->change();
            $table->text('other_income_source')->nullable()->change();
            $table->text('loan_purpose_other')->nullable()->change();
            $table->text('office_location')->nullable()->change();
            $table->text('repayment_means')->nullable()->change();
            $table->text('spouse_work_place')->nullable()->change();
            $table->text('business_location')->nullable()->change();

            // Now add nick_name
            $table->string('nick_name')->nullable()->after('full_name');
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn('nick_name');
            // We won't revert TEXT to VARCHAR in down() to avoid re-triggering size errors
        });
    }
};
