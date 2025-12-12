<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ConsultationReview extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'patient_id',
        'linked_consultation_id',
        'reviewing_clinician_id',
        'review_date',
        'visit_type',
        'bp_systolic',
        'bp_diastolic',
        'heart_rate',
        'respiratory_rate',
        'temperature',
        'weight',
        'height',
        'bmi',
        'vitals_notes',
        'subjective',
        'objective',
        'treatment_response',
        'medication_adherence',
        'therapy_engagement',
        'side_effects',
        'new_symptoms',
        'clinical_assessment',
        'plan_changes',
        'continue_current_treatment',
        'goals_progress',
        'new_interventions',
        'next_steps',
    ];

    protected $casts = [
        'review_date' => 'date',
        'continue_current_treatment' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });

        // Auto-calculate BMI
        static::saving(function ($model) {
            if ($model->weight && $model->height) {
                $heightInMeters = $model->height / 100;
                $model->bmi = round($model->weight / ($heightInMeters * $heightInMeters), 1);
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function linkedConsultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'linked_consultation_id');
    }

    public function reviewingClinician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewing_clinician_id');
    }
}
