<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->decimal('collateral_total_value', 15, 2)->nullable()->after('other_income_financial');
            $table->json('other_collaterals')->nullable()->after('collateral_total_value');
            $table->text('proof_of_address_description')->nullable()->after('attachments');
        });
    }

    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn([
                'collateral_total_value',
                'other_collaterals',
                'proof_of_address_description',
            ]);
        });
    }
};
