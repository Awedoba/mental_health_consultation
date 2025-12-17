<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Billing extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'patient_id',
        'consultation_id',
        'billing_date',
        'service_type',
        'amount',
        'nhis_covered',
        'nhis_amount',
        'patient_amount',
        'payment_status',
        'payment_date',
        'payment_method',
        'invoice_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'nhis_amount' => 'decimal:2',
        'patient_amount' => 'decimal:2',
        'nhis_covered' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            
            // Auto-generate invoice number if not provided
            if (empty($model->invoice_number)) {
                $model->invoice_number = 'INV-' . strtoupper(Str::random(8)) . '-' . date('Ymd');
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
