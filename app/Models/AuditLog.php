<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'event_category',
        'event_type',
        'entity_type',
        'entity_id',
        'action',
        'changes',
        'status',
        'error_message',
        'hash_chain',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'changes' => 'array',
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            
            if (empty($model->timestamp)) {
                $model->timestamp = now();
            }
            
            // Generate hash chain
            $previousHash = static::latest('timestamp')->first()?->hash_chain ?? 'initial';
            $hashData = $model->id . $model->timestamp->toIso8601String() . $previousHash . ($model->user_id ?? '') . $model->event_type . ($model->entity_id ?? '');
            $model->hash_chain = hash('sha256', $hashData);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
