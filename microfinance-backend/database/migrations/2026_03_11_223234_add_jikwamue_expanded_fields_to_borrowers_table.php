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
            // Section 3: Collateral - Vehicle
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_owner')) {
                $table->text('collateral_vehicle_owner')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_type')) {
                $table->text('collateral_vehicle_type')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_reg_no')) {
                $table->text('collateral_vehicle_reg_no')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_engine_no')) {
                $table->text('collateral_vehicle_engine_no')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_chassis_no')) {
                $table->text('collateral_vehicle_chassis_no')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_model')) {
                $table->text('collateral_vehicle_model')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_color')) {
                $table->text('collateral_vehicle_color')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_insurance_type')) {
                $table->text('collateral_vehicle_insurance_type')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_insurance_provider')) {
                $table->text('collateral_vehicle_insurance_provider')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_value')) {
                $table->decimal('collateral_vehicle_value', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_vehicle_forced_sale_value')) {
                $table->decimal('collateral_vehicle_forced_sale_value', 15, 2)->nullable();
            }

            // Section 3: Collateral - Land
            if (!Schema::hasColumn('borrowers', 'collateral_land_type')) {
                $table->text('collateral_land_type')->nullable(); // Kiwanja, Nyumba etc.
            }
            if (!Schema::hasColumn('borrowers', 'collateral_land_owner')) {
                $table->text('collateral_land_owner')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_land_kitalu')) {
                $table->text('collateral_land_kitalu')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_land_plot_no')) {
                $table->text('collateral_land_plot_no')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_land_description')) {
                $table->text('collateral_land_description')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_land_value')) {
                $table->decimal('collateral_land_value', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'collateral_land_forced_sale_value')) {
                $table->decimal('collateral_land_forced_sale_value', 15, 2)->nullable();
            }

            // Section 4: Business details
            if (!Schema::hasColumn('borrowers', 'project_description')) {
                $table->text('project_description')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'business_legal_status')) {
                $table->text('business_legal_status')->nullable(); // Company, Partnership, Sole
            }
            if (!Schema::hasColumn('borrowers', 'business_occupancy')) {
                $table->text('business_occupancy')->nullable(); // Owned / Rented
            }
            if (!Schema::hasColumn('borrowers', 'landlord_name')) {
                $table->text('landlord_name')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'landlord_phone')) {
                $table->text('landlord_phone')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'landlord_address')) {
                $table->text('landlord_address')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'rent_duration')) {
                $table->text('rent_duration')->nullable();
            }
            if (!Schema::hasColumn('borrowers', 'previous_business_location')) {
                $table->text('previous_business_location')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn([
                'collateral_vehicle_owner', 'collateral_vehicle_type', 'collateral_vehicle_reg_no',
                'collateral_vehicle_engine_no', 'collateral_vehicle_chassis_no', 'collateral_vehicle_model',
                'collateral_vehicle_color', 'collateral_vehicle_insurance_type', 'collateral_vehicle_insurance_provider',
                'collateral_vehicle_value', 'collateral_vehicle_forced_sale_value',
                'collateral_land_type', 'collateral_land_owner', 'collateral_land_kitalu',
                'collateral_land_plot_no', 'collateral_land_description', 'collateral_land_value',
                'collateral_land_forced_sale_value', 'project_description', 'business_legal_status',
                'business_occupancy', 'landlord_name', 'landlord_phone', 'landlord_address',
                'rent_duration', 'previous_business_location'
            ]);
        });
    }
};
