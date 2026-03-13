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
        DB::statement('ALTER TABLE borrowers ROW_FORMAT=DYNAMIC');
        
        Schema::table('borrowers', function (Blueprint $table) {
            // Group Logistics
            if (!Schema::hasColumn('borrowers', 'group_established_date')) {
                $table->date('group_established_date')->nullable()->after('group_members_count');
            }
            if (!Schema::hasColumn('borrowers', 'group_meeting_place')) {
                $table->text('group_meeting_place')->nullable()->after('group_established_date');
            }
            if (!Schema::hasColumn('borrowers', 'group_region')) {
                $table->string('group_region', 100)->nullable()->after('group_meeting_place');
            }
            if (!Schema::hasColumn('borrowers', 'group_district')) {
                $table->string('group_district', 100)->nullable()->after('group_region');
            }
            if (!Schema::hasColumn('borrowers', 'group_ward')) {
                $table->string('group_ward', 100)->nullable()->after('group_district');
            }
            if (!Schema::hasColumn('borrowers', 'group_village')) {
                $table->string('group_village', 100)->nullable()->after('group_ward');
            }
            
            // Group Officials
            if (!Schema::hasColumn('borrowers', 'group_chairman_name')) {
                $table->text('group_chairman_name')->nullable()->after('group_meeting_place');
            }
            if (!Schema::hasColumn('borrowers', 'group_chairman_phone')) {
                $table->text('group_chairman_phone')->nullable()->after('group_chairman_name');
            }
            if (!Schema::hasColumn('borrowers', 'group_secretary_name')) {
                $table->text('group_secretary_name')->nullable()->after('group_chairman_phone');
            }
            if (!Schema::hasColumn('borrowers', 'group_secretary_phone')) {
                $table->text('group_secretary_phone')->nullable()->after('group_secretary_name');
            }
            if (!Schema::hasColumn('borrowers', 'group_treasurer_name')) {
                $table->text('group_treasurer_name')->nullable()->after('group_secretary_phone');
            }
            if (!Schema::hasColumn('borrowers', 'group_treasurer_phone')) {
                $table->text('group_treasurer_phone')->nullable()->after('group_treasurer_name');
            }
            
            // Group Financials
            if (!Schema::hasColumn('borrowers', 'group_bank_account')) {
                $table->text('group_bank_account')->nullable()->after('group_treasurer_phone');
            }
            if (!Schema::hasColumn('borrowers', 'group_bank_name')) {
                $table->text('group_bank_name')->nullable()->after('group_bank_account');
            }
            
            // Member Specific to Group
            if (!Schema::hasColumn('borrowers', 'date_joined_group')) {
                $table->date('date_joined_group')->nullable()->after('group_bank_name');
            }
            
            // Local Government
            if (!Schema::hasColumn('borrowers', 'local_govt_chairman_name')) {
                $table->text('local_govt_chairman_name')->nullable()->after('date_joined_group');
            }
            if (!Schema::hasColumn('borrowers', 'local_govt_chairman_phone')) {
                $table->text('local_govt_chairman_phone')->nullable()->after('local_govt_chairman_name');
            }
            if (!Schema::hasColumn('borrowers', 'local_govt_chairman_title')) {
                $table->text('local_govt_chairman_title')->nullable()->after('local_govt_chairman_phone');
            }
            
            // Register & Liability
            if (!Schema::hasColumn('borrowers', 'group_members_list')) {
                $table->longText('group_members_list')->nullable()->after('local_govt_chairman_title');
            }
            if (!Schema::hasColumn('borrowers', 'group_liability_agreed')) {
                $table->boolean('group_liability_agreed')->default(false)->after('group_members_list');
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
                'group_established_date',
                'group_meeting_place',
                'group_chairman_name',
                'group_chairman_phone',
                'group_secretary_name',
                'group_secretary_phone',
                'group_treasurer_name',
                'group_treasurer_phone',
                'group_bank_account',
                'group_bank_name',
                'date_joined_group',
                'local_govt_chairman_name',
                'local_govt_chairman_phone',
                'local_govt_chairman_title',
                'group_members_list',
                'group_liability_agreed'
            ]);
        });
    }
};
