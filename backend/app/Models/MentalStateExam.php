<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MentalStateExam extends Model
{
    use HasFactory;

    protected $table = 'mental_state_exam';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'consultation_id',
        'general_appearance',
        'appearance_clothing',
        'posture',
        'grooming',
        'behavior',
        'movement',
        'attitude',
        'speech',
        'sense_of_self',
        'motivation',
        'level_of_consciousness',
        'eye_contact',
        'psychomotor_activity',
        'attitude_toward_examiner',
        'behavior_notes',
        'speech_rate',
        'speech_volume',
        'speech_tone',
        'speech_fluency',
        'speech_articulation',
        'speech_notes',
        'reported_mood',
        'observed_affect',
        'affect_range',
        'affect_congruence',
        'mood_notes',
        'mood',
        'affect',
        'thought_organization',
        'thought_coherence',
        'delusions',
        'delusion_details',
        'obsessions',
        'compulsions',
        'phobias',
        'suicidal_ideation',
        'homicidal_ideation',
        'thought_notes',
        'hallucinations',
        'hallucination_details',
        'illusions',
        'depersonalization',
        'derealization',
        'perception_notes',
        'orientation_person',
        'orientation_person_details',
        'orientation_place',
        'orientation_place_details',
        'orientation_time',
        'orientation_time_details',
        'orientation_details',
        'orientation_situation',
        'attention_concentration',
        'memory_immediate',
        'memory_recent',
        'memory_remote',
        'fund_of_knowledge',
        'abstraction',
        'cognition_notes',
        'general_information',
        'physical_coordination',
        'insight',
        'insight_description',
        'judgment',
        'judgment_description',
    ];

    protected $casts = [
        'delusions' => 'array',
        'hallucinations' => 'array',
        'obsessions' => 'boolean',
        'compulsions' => 'boolean',
        'illusions' => 'boolean',
        'depersonalization' => 'boolean',
        'derealization' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }
}
