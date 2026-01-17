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
        Schema::table('patients', function (Blueprint $table) {
            // Add new fields
            $table->string('religion', 50)->nullable()->after('education_level');
            $table->string('town', 100)->nullable()->after('country');
            $table->enum('nhis_status', ['insured', 'uninsured'])->default('uninsured')->after('town');
            $table->string('relative_name', 100)->nullable()->after('nhis_status');
            $table->string('relative_relationship', 50)->nullable()->after('relative_name');
            $table->string('relative_phone', 20)->nullable()->after('relative_relationship');
            $table->time('assessment_time')->nullable()->after('relative_phone');
            
            // Add landmark field (keeping address_line1 for backward compatibility initially)
            $table->string('landmark', 200)->nullable()->after('address_line2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'religion',
                'town',
                'nhis_status',
                'relative_name',
                'relative_relationship',
                'relative_phone',
                'assessment_time',
                'landmark',
            ]);
        });
    }
};
