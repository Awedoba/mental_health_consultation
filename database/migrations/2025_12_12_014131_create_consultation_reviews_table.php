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
        Schema::create('consultation_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id');
            $table->uuid('linked_consultation_id');
            $table->uuid('reviewing_clinician_id');
            $table->date('review_date');
            $table->string('visit_type'); // 'scheduled_followup', 'unscheduled', 'crisis_visit', 'medication_check'
            
            // Vitals
            $table->integer('bp_systolic')->nullable();
            $table->integer('bp_diastolic')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('bmi', 4, 1)->nullable();
            $table->text('vitals_notes')->nullable();
            
            // Progress Assessment
            $table->text('subjective');
            $table->text('objective');
            $table->string('treatment_response'); // 'significant_improvement', 'moderate_improvement', 'minimal_improvement', 'no_change', 'worsening'
            $table->string('medication_adherence')->nullable(); // 'fully_adherent', 'mostly_adherent', 'partially_adherent', 'non_adherent', 'na'
            $table->string('therapy_engagement')->nullable(); // 'excellent', 'good', 'fair', 'poor', 'na'
            $table->text('side_effects')->nullable();
            $table->text('new_symptoms')->nullable();
            
            // Assessment & Plan
            $table->text('clinical_assessment');
            $table->text('plan_changes')->nullable();
            $table->boolean('continue_current_treatment')->default(false);
            $table->text('goals_progress')->nullable();
            $table->text('new_interventions')->nullable();
            $table->text('next_steps');
            
            $table->timestamps();
            
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('restrict');
            $table->foreign('linked_consultation_id')->references('id')->on('consultations')->onDelete('restrict');
            $table->foreign('reviewing_clinician_id')->references('id')->on('users')->onDelete('restrict');
            $table->index(['patient_id', 'review_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_reviews');
    }
};
