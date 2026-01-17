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
        Schema::create('consultations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id');
            $table->uuid('primary_clinician_id');
            $table->date('consultation_date');
            $table->time('consultation_time');
            $table->string('session_type'); // 'initial_assessment', 'follow_up', 'crisis_intervention', 'therapy_session', 'medication_review'
            $table->integer('session_duration')->nullable();
            $table->text('chief_complaint');
            $table->text('history_present_illness');
            $table->text('past_psychiatric_history')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('family_history')->nullable();
            $table->text('social_history')->nullable();
            $table->text('current_medications')->nullable();
            $table->text('allergies')->nullable();
            $table->string('risk_assessment'); // 'low', 'moderate', 'high'
            $table->boolean('safety_plan_required')->default(false);
            $table->text('clinical_summary');
            $table->text('treatment_interventions')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('restrict');
            $table->foreign('primary_clinician_id')->references('id')->on('users')->onDelete('restrict');
            $table->index(['patient_id', 'consultation_date']);
            $table->index(['primary_clinician_id', 'consultation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
