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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id');
            $table->uuid('consultation_id')->nullable();
            $table->date('prescription_date');
            $table->uuid('prescribed_by');
            $table->uuid('medication_id');
            $table->string('dosage', 100);
            $table->string('frequency', 100);
            $table->string('duration', 100);
            $table->integer('quantity')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('refills')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('restrict');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
            $table->foreign('prescribed_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('medication_id')->references('id')->on('medications')->onDelete('restrict');
            
            $table->index(['patient_id', 'prescription_date']);
            $table->index(['consultation_id']);
            $table->index('is_active');
            $table->index('prescription_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
