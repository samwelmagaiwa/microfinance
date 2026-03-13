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
            $table->text('risk_description')->nullable()->after('risk_assessment');
            $table->text('board_decision_remarks')->nullable()->after('board_decision');
            $table->date('board_decision_date')->nullable()->after('board_decision_remarks');
            $table->string('board_member_name')->nullable()->after('board_decision_date');
            $table->text('md_remarks')->nullable()->after('gm_remarks');
            $table->string('md_name')->nullable()->after('md_remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            //
        });
    }
};
