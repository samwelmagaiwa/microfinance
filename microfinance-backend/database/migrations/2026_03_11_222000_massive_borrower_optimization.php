<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE borrowers ROW_FORMAT=DYNAMIC');
        }

        Schema::table('borrowers', function (Blueprint $table) {
            $colsToText = [
                'full_name', 'email', 'employer_name', 'occupation', 'work_station',
                'spouse_name', 'spouse_employer', 'bank_name', 'mobile_money_number',
                'residence_description', 'employer_address', 'officer_remarks',
                'loan_manager_remarks', 'gm_remarks', 'board_decision_remarks',
                'group_name', 'group_position', 'postal_address', 'region', 'district', 'ward', 'village'
            ];

            foreach ($colsToText as $col) {
                if (Schema::hasColumn('borrowers', $col)) {
                    $table->text($col)->nullable()->change();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optimization is non-reversible usually
    }
};
