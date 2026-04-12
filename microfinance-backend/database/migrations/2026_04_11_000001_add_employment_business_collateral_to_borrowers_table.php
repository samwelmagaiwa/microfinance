<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            // Employment status - 'ndiyo' (employed) or 'hapana' (not employed)
            $table->enum('is_employed', ['ndiyo', 'hapana'])->nullable()->after('business_license_number');

            // Business status - 'ndiyo' (has business) or 'hapana' (no business)
            $table->enum('has_business', ['ndiyo', 'hapana'])->nullable()->after('is_employed');

            // Collateral information (if not employed, collateral is required)
            $table->string('collateral_type')->nullable()->after('has_business');
            $table->string('collateral_registration_number')->nullable()->after('collateral_type');
            $table->string('collateral_ownership')->nullable()->after('collateral_registration_number');
            $table->decimal('collateral_current_value', 15, 2)->nullable()->after('collateral_ownership');
            $table->text('collateral_appearance')->nullable()->after('collateral_current_value');
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $columns = [
                'is_employed', 'has_business',
                'collateral_type', 'collateral_registration_number', 'collateral_ownership',
                'collateral_current_value', 'collateral_appearance'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('borrowers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};