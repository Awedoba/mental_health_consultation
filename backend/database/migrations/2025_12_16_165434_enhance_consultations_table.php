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
        Schema::table('consultations', function (Blueprint $table) {
            // Add new history fields
            $table->text('personal_history')->nullable()->after('social_history');
            $table->text('occupational_marital_history')->nullable()->after('personal_history');
            $table->text('substance_abuse_history')->nullable()->after('occupational_marital_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn([
                'personal_history',
                'occupational_marital_history',
                'substance_abuse_history',
            ]);
        });
    }
};
