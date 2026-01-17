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
        Schema::create('management_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consultation_id')->unique();
            $table->json('treatment_modalities');
            $table->string('psychotherapy_type')->nullable(); // 'cbt', 'dbt', 'psychodynamic', 'supportive', 'motivational_interviewing', 'other'
            $table->string('therapy_frequency')->nullable(); // 'weekly', 'biweekly', 'monthly', 'as_needed'
            $table->text('treatment_goals');
            $table->text('clinical_recommendations');
            $table->text('patient_education')->nullable();
            $table->text('referrals')->nullable();
            $table->date('next_visit_date')->nullable();
            $table->string('next_visit_purpose')->nullable(); // 'medication_review', 'therapy_session', 'progress_check', 'crisis_followup', 'discharge_planning'
            $table->string('follow_up_interval')->nullable(); // '1_week', '2_weeks', '1_month', '3_months', '6_months', 'as_needed'
            $table->boolean('urgent_follow_up')->default(false);
            $table->text('follow_up_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('management_plans');
    }
};
