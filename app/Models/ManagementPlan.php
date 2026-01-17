<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ManagementPlan extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'consultation_id',
        'treatment_modalities',
        'psychotherapy_type',
        'therapy_frequency',
        'treatment_goals',
        'clinical_recommendations',
        'patient_education',
        'referrals',
        'next_visit_date',
        'next_visit_purpose',
        'follow_up_interval',
        'urgent_follow_up',
        'follow_up_notes',
    ];

    protected $casts = [
        'treatment_modalities' => 'array',
        'next_visit_date' => 'date',
        'urgent_follow_up' => 'boolean',
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
