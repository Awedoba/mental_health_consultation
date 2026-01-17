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
        Schema::table('consultation_reviews', function (Blueprint $table) {
            // Add new review fields
            $table->text('presenting_complaints')->nullable()->after('visit_type');
            $table->text('oe')->nullable()->after('presenting_complaints'); // Objective examination
            $table->text('odq')->nullable()->after('oe'); // Objective data/quantitative
            $table->text('recommendation')->nullable()->after('next_steps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultation_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'presenting_complaints',
                'oe',
                'odq',
                'recommendation',
            ]);
        });
    }
};
