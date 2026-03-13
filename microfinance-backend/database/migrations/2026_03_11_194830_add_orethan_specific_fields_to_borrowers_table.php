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
            // Employment Specific (Mirrors PDF)
            $table->string('pf_number')->nullable()->after('employer_phone');
            $table->date('retirement_date')->nullable()->after('pf_number');
            $table->string('work_station')->nullable()->after('retirement_date');
            
            // Group Specific (Mirrors PDF)
            $table->string('group_name')->nullable()->after('work_station');
            $table->string('group_id_number')->nullable()->after('group_name');
            $table->string('group_position')->nullable()->after('group_id_number');
            $table->integer('group_members_count')->nullable()->after('group_position');
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn([
                'pf_number', 
                'retirement_date', 
                'work_station',
                'group_name', 
                'group_id_number', 
                'group_position', 
                'group_members_count'
            ]);
        });
    }
};
