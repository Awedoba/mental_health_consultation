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
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->date('date_of_birth');
            $table->string('gender'); // 'male', 'female', 'non_binary', 'prefer_not_say', 'other'
            $table->string('phone_number', 20);
            $table->string('email', 100)->nullable();
            $table->string('address_line1', 100);
            $table->string('address_line2', 100)->nullable();
            $table->string('city', 50);
            $table->string('state_province', 50);
            $table->string('postal_code', 20);
            $table->string('country', 50);
            $table->string('marital_status')->nullable(); // 'single', 'married', 'divorced', 'widowed', 'separated'
            $table->string('occupation', 100)->nullable();
            $table->string('education_level')->nullable(); // 'none', 'primary', 'secondary', 'undergraduate', 'graduate', 'doctoral'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            
            $table->index(['last_name', 'first_name', 'date_of_birth']);
            $table->index('is_active');
            $table->unique(['first_name', 'last_name', 'date_of_birth'], 'patients_unique_name_dob');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
