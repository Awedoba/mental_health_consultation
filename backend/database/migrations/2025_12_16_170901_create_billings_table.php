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
        Schema::create('billings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id');
            $table->uuid('consultation_id')->nullable();
            $table->date('billing_date');
            $table->enum('service_type', ['consultation', 'home_visit', 'medication', 'other'])->default('consultation');
            $table->decimal('amount', 10, 2);
            $table->boolean('nhis_covered')->default(false);
            $table->decimal('nhis_amount', 10, 2)->nullable();
            $table->decimal('patient_amount', 10, 2)->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'waived'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer', 'nhis'])->nullable();
            $table->string('invoice_number', 50)->unique();
            $table->text('notes')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('restrict');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            
            $table->index(['patient_id', 'billing_date']);
            $table->index(['consultation_id']);
            $table->index('payment_status');
            $table->index('billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
