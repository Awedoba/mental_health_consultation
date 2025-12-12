<?php

namespace App\Http\Middleware;

use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log API requests (excluding auth/login to prevent password logging)
        if ($request->is('api/*') && !$request->is('api/auth/login')) {
            $action = strtolower($request->method());
            $entityType = $this->getEntityType($request->path());
            $entityId = $this->getEntityId($request);

            AuditLogService::log(
                eventCategory: $this->getEventCategory($request->path()),
                eventType: $this->getEventType($request->path(), $action),
                action: $action,
                entityType: $entityType,
                entityId: $entityId,
                status: $response->getStatusCode() >= 200 && $response->getStatusCode() < 300 ? 'success' : 'failure',
                request: $request
            );
        }

        return $response;
    }

    private function getEntityType(string $path): ?string
    {
        if (str_contains($path, 'patients')) return 'patient';
        if (str_contains($path, 'consultations')) return 'consultation';
        if (str_contains($path, 'users')) return 'user';
        return null;
    }

    private function getEntityId(Request $request): ?string
    {
        $route = $request->route();
        return $route?->parameter('patient')?->id 
            ?? $route?->parameter('consultation')?->id
            ?? $route?->parameter('user')?->id
            ?? null;
    }

    private function getEventCategory(string $path): string
    {
        if (str_contains($path, 'auth')) return 'authentication';
        if (str_contains($path, 'patients')) return 'patient_records';
        if (str_contains($path, 'consultations') || str_contains($path, 'mse') || str_contains($path, 'diagnoses')) return 'clinical_data';
        if (str_contains($path, 'reports') || str_contains($path, 'dashboard')) return 'reports';
        if (str_contains($path, 'admin')) return 'system_admin';
        return 'data_access';
    }

    private function getEventType(string $path, string $action): string
    {
        $resource = $this->getResourceName($path);
        return $resource ? "{$resource}_{$action}" : 'api_request';
    }

    private function getResourceName(string $path): ?string
    {
        if (str_contains($path, 'patients')) return 'patient';
        if (str_contains($path, 'consultations')) return 'consultation';
        if (str_contains($path, 'mse')) return 'mse';
        if (str_contains($path, 'diagnoses')) return 'diagnosis';
        if (str_contains($path, 'users')) return 'user';
        return null;
    }
}
