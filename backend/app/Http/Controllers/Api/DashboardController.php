<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    /**
     * Get dashboard data based on user role
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get basic stats
        $stats = $this->getStats($user);
        
        // Get recent consultations
        $recentConsultations = $this->getRecentConsultations($user);

        return $this->success([
            'stats' => $stats,
            'recentConsultations' => $recentConsultations,
        ], 'Dashboard data retrieved');
    }

    /**
     * Get statistics based on user role
     */
    private function getStats($user): array
    {
        if ($user->role === 'admin') {
            return [
                'totalPatients' => Patient::where('is_active', true)->count(),
                'consultationsThisMonth' => Consultation::whereMonth('consultation_date', now()->month)
                    ->whereYear('consultation_date', now()->year)
                    ->count(),
                'pendingReviews' => Consultation::where('is_locked', false)
                    ->where('consultation_date', '<', now()->subDays(7))
                    ->count(),
                'activeClinicians' => User::where('role', 'clinician')
                    ->where('is_active', true)
                    ->count(),
            ];
        }

        // Clinician stats
        return [
            'totalPatients' => Patient::whereHas('consultations', function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id);
            })->count(),
            'consultationsThisMonth' => Consultation::where('primary_clinician_id', $user->id)
                ->whereMonth('consultation_date', now()->month)
                ->whereYear('consultation_date', now()->year)
                ->count(),
            'pendingReviews' => Consultation::where('primary_clinician_id', $user->id)
                ->where('is_locked', false)
                ->where('consultation_date', '<', now()->subDays(7))
                ->count(),
            'activeClinicians' => 0, // Not shown to clinicians
        ];
    }

    /**
     * Get recent consultations
     */
    private function getRecentConsultations($user): array
    {
        $query = Consultation::with(['patient', 'primaryClinician'])
            ->orderBy('consultation_date', 'desc')
            ->orderBy('consultation_time', 'desc')
            ->limit(10);

        // Filter by clinician if not admin
        if ($user->role !== 'admin') {
            $query->where('primary_clinician_id', $user->id);
        }

        return $query->get()->map(function ($consultation) {
            return [
                'id' => $consultation->id,
                'patient_id' => $consultation->patient_id,
                'patient_name' => $consultation->patient 
                    ? $consultation->patient->first_name . ' ' . $consultation->patient->last_name
                    : 'N/A',
                'primary_clinician_id' => $consultation->primary_clinician_id,
                'clinician_name' => $consultation->primaryClinician
                    ? $consultation->primaryClinician->first_name . ' ' . $consultation->primaryClinician->last_name
                    : 'N/A',
                'consultation_date' => $consultation->consultation_date,
                'consultation_time' => $consultation->consultation_time,
                'session_type' => $consultation->session_type,
                'chief_complaint' => $consultation->chief_complaint,
                'risk_assessment' => $consultation->risk_assessment,
                'is_locked' => $consultation->is_locked,
                'created_at' => $consultation->created_at->toISOString(),
                'updated_at' => $consultation->updated_at->toISOString(),
            ];
        })->toArray();
    }
}
