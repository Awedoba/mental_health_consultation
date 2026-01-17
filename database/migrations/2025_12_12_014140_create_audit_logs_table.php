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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('timestamp')->useCurrent();
            $table->uuid('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('event_category'); // 'authentication', 'patient_records', 'clinical_data', 'data_access', 'reports', 'system_admin'
            $table->string('event_type', 50);
            $table->string('entity_type')->nullable(); // 'user', 'patient', 'consultation', 'mse', 'diagnosis', 'review', 'system'
            $table->string('entity_id', 255)->nullable();
            $table->string('action'); // 'create', 'read', 'update', 'delete', 'login', 'logout', 'export'
            $table->json('changes')->nullable();
            $table->string('status'); // 'success', 'failure', 'partial'
            $table->text('error_message')->nullable();
            $table->string('hash_chain', 64);
            
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['timestamp']);
            $table->index(['user_id', 'timestamp']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['event_category', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
