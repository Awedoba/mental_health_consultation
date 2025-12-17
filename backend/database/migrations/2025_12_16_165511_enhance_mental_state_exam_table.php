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
        Schema::table('mental_state_exam', function (Blueprint $table) {
            // Enhanced Appearance & Behavior fields
            $table->string('appearance_clothing')->nullable()->after('general_appearance');
            $table->string('posture')->nullable()->after('appearance_clothing');
            $table->text('grooming')->nullable()->after('posture');
            $table->text('behavior')->nullable()->after('grooming');
            $table->text('movement')->nullable()->after('behavior');
            $table->text('attitude')->nullable()->after('movement');
            $table->text('speech')->nullable()->after('attitude');
            $table->text('sense_of_self')->nullable()->after('speech');
            $table->text('motivation')->nullable()->after('sense_of_self');
            
            // Enhanced Cognitive Functioning fields
            $table->text('general_information')->nullable()->after('cognition_notes');
            $table->text('physical_coordination')->nullable()->after('general_information');
            $table->text('mood')->nullable()->after('physical_coordination');
            $table->text('affect')->nullable()->after('mood');
            
            // Orientation details (text fields for person/place/time)
            $table->text('orientation_person_details')->nullable()->after('orientation_person');
            $table->text('orientation_place_details')->nullable()->after('orientation_place');
            $table->text('orientation_time_details')->nullable()->after('orientation_time');
            $table->text('orientation_details')->nullable()->after('orientation_time_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mental_state_exam', function (Blueprint $table) {
            $table->dropColumn([
                'appearance_clothing',
                'posture',
                'grooming',
                'behavior',
                'movement',
                'attitude',
                'speech',
                'sense_of_self',
                'motivation',
                'general_information',
                'physical_coordination',
                'mood',
                'affect',
                'orientation_person_details',
                'orientation_place_details',
                'orientation_time_details',
                'orientation_details',
            ]);
        });
    }
};
