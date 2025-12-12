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
        Schema::create('mental_state_exam', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consultation_id')->unique();
            
            // Appearance & Behavior
            $table->string('general_appearance'); // 'well_groomed', 'disheveled', 'bizarre', 'age_appropriate', 'inappropriately_dressed'
            $table->string('level_of_consciousness'); // 'alert', 'drowsy', 'lethargic', 'stuporous', 'comatose'
            $table->string('eye_contact'); // 'appropriate', 'avoidant', 'intense', 'fleeting'
            $table->string('psychomotor_activity'); // 'normal', 'agitated', 'restless', 'retarded', 'hyperactive', 'catatonic'
            $table->string('attitude_toward_examiner'); // 'cooperative', 'guarded', 'hostile', 'suspicious', 'evasive', 'seductive'
            $table->text('behavior_notes')->nullable();
            
            // Speech & Language
            $table->string('speech_rate'); // 'normal', 'rapid', 'slow', 'pressured'
            $table->string('speech_volume'); // 'normal', 'loud', 'soft', 'whispered'
            $table->string('speech_tone'); // 'normal', 'monotone', 'dramatic', 'flat'
            $table->string('speech_fluency'); // 'fluent', 'non_fluent', 'hesitant', 'stuttering'
            $table->string('speech_articulation'); // 'clear', 'slurred', 'mumbled'
            $table->text('speech_notes')->nullable();
            
            // Mood & Affect
            $table->string('reported_mood', 100);
            $table->string('observed_affect'); // 'euthymic', 'depressed', 'anxious', 'irritable', 'euphoric', 'angry', 'labile'
            $table->string('affect_range'); // 'full', 'restricted', 'blunted', 'flat'
            $table->string('affect_congruence'); // 'congruent', 'incongruent'
            $table->text('mood_notes')->nullable();
            
            // Thought Process/Content
            $table->string('thought_organization'); // 'logical', 'goal_directed', 'circumstantial', 'tangential', 'loose_associations', 'flight_of_ideas', 'thought_blocking'
            $table->string('thought_coherence'); // 'coherent', 'incoherent', 'disorganized'
            $table->json('delusions')->nullable();
            $table->text('delusion_details')->nullable();
            $table->boolean('obsessions')->default(false);
            $table->boolean('compulsions')->default(false);
            $table->text('phobias')->nullable();
            $table->string('suicidal_ideation'); // 'none', 'passive', 'active_no_plan', 'active_with_plan', 'active_with_intent'
            $table->string('homicidal_ideation'); // 'none', 'passive', 'active_no_plan', 'active_with_plan', 'active_with_intent'
            $table->text('thought_notes')->nullable();
            
            // Perception
            $table->json('hallucinations')->nullable();
            $table->text('hallucination_details')->nullable();
            $table->boolean('illusions')->default(false);
            $table->boolean('depersonalization')->default(false);
            $table->boolean('derealization')->default(false);
            $table->text('perception_notes')->nullable();
            
            // Cognition & Sensorium
            $table->string('orientation_person'); // 'oriented', 'disoriented'
            $table->string('orientation_place'); // 'oriented', 'disoriented'
            $table->string('orientation_time'); // 'oriented', 'disoriented'
            $table->string('orientation_situation'); // 'oriented', 'disoriented'
            $table->string('attention_concentration'); // 'intact', 'impaired', 'grossly_impaired'
            $table->string('memory_immediate'); // 'intact', 'impaired', 'grossly_impaired'
            $table->string('memory_recent'); // 'intact', 'impaired', 'grossly_impaired'
            $table->string('memory_remote'); // 'intact', 'impaired', 'grossly_impaired'
            $table->string('fund_of_knowledge')->nullable(); // 'average', 'above_average', 'below_average'
            $table->string('abstraction')->nullable(); // 'intact', 'concrete', 'impaired'
            $table->text('cognition_notes')->nullable();
            
            // Insight & Judgment
            $table->string('insight'); // 'good', 'fair', 'poor', 'absent'
            $table->text('insight_description')->nullable();
            $table->string('judgment'); // 'good', 'fair', 'poor', 'grossly_impaired'
            $table->text('judgment_description')->nullable();
            
            $table->timestamps();
            
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_state_exam');
    }
};
