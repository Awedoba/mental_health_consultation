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
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consultation_id');
            $table->boolean('is_primary');
            $table->string('icd10_code', 10);
            $table->string('diagnosis_description', 500);
            $table->string('diagnosis_status'); // 'provisional', 'confirmed', 'rule_out'
            $table->string('severity')->nullable(); // 'mild', 'moderate', 'severe'
            $table->date('onset_date')->nullable();
            $table->text('rationale')->nullable();
            $table->string('likelihood')->nullable(); // 'high', 'moderate', 'low'
            $table->timestamps();
            
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
            $table->index(['consultation_id', 'is_primary']);
            $table->index('icd10_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
    }
};
