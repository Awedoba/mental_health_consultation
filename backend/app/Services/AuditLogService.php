<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
    /**
     * Log an audit event
     */
    public static function log(
        string $eventCategory,
        string $eventType,
        string $action,
        ?string $entityType = null,
        ?string $entityId = null,
        ?array $changes = null,
        string $status = 'success',
        ?string $errorMessage = null,
        ?Request $request = null
    ): void {
        try {
            $user = $request?->user();
            $previousHash = AuditLog::latest('timestamp')->first()?->hash_chain ?? 'initial';
            
            $hashData = (string) \Illuminate\Support\Str::uuid() 
                . now()->toIso8601String() 
                . $previousHash 
                . ($user?->id ?? '') 
                . $eventType 
                . ($entityId ?? '');
            
            $hashChain = hash('sha256', $hashData);

            AuditLog::create([
                'user_id' => $user?->id,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'event_category' => $eventCategory,
                'event_type' => $eventType,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'action' => $action,
                'changes' => $changes,
                'status' => $status,
                'error_message' => $errorMessage,
                'hash_chain' => $hashChain,
            ]);
        } catch (\Exception $e) {
            // Log to Laravel log if audit logging fails (don't break the application)
            Log::error('Audit logging failed: ' . $e->getMessage());
        }
    }
}

