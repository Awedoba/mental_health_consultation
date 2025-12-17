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
        Schema::create('home_visits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id')->nullable();
            $table->string('client_name', 200);
            $table->integer('age')->nullable();
            $table->enum('sex', ['male', 'female', 'other'])->nullable();
            $table->string('community_location', 200);
            $table->string('contact', 20);
            $table->date('visit_date');
            $table->time('visit_time')->nullable();
            $table->uuid('clinician_id');
            $table->text('diagnosis_condition')->nullable();
            $table->text('medication_prescription')->nullable();
            $table->text('observations')->nullable();
            $table->text('impression')->nullable();
            $table->text('management')->nullable();
            $table->text('recommendation')->nullable();
            $table->timestamps();
            
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->foreign('clinician_id')->references('id')->on('users')->onDelete('restrict');
            
            $table->index(['patient_id', 'visit_date']);
            $table->index(['clinician_id', 'visit_date']);
            $table->index('visit_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_visits');
    }
};
