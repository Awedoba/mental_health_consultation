<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Consultation extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'patient_id',
        'primary_clinician_id',
        'consultation_date',
        'consultation_time',
        'session_type',
        'session_duration',
        'chief_complaint',
        'history_present_illness',
        'past_psychiatric_history',
        'medical_history',
        'family_history',
        'social_history',
        'current_medications',
        'allergies',
        'risk_assessment',
        'safety_plan_required',
        'clinical_summary',
        'treatment_interventions',
        'clinical_notes',
        'is_locked',
    ];

    protected $casts = [
        'consultation_date' => 'date',
        'consultation_time' => 'datetime',
        'safety_plan_required' => 'boolean',
        'is_locked' => 'boolean',
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

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function primaryClinician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_clinician_id');
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(ConsultationCollaborator::class);
    }

    public function mentalStateExam(): HasOne
    {
        return $this->hasOne(MentalStateExam::class);
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function managementPlan(): HasOne
    {
        return $this->hasOne(ManagementPlan::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ConsultationReview::class, 'linked_consultation_id');
    }
}
