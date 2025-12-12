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

        if ($user->role === 'admin') {
            return $this->adminDashboard($request);
        }

        return $this->clinicianDashboard($request);
    }

    /**
     * Admin dashboard metrics
     */
    private function adminDashboard(Request $request): JsonResponse
    {
        $totalPatients = Patient::where('is_active', true)->count();
        $activeClinicians = User::where('role', 'clinician')->where('is_active', true)->count();
        $newPatients30Days = Patient::where('created_at', '>=', now()->subDays(30))->count();
        $consultationsThisMonth = Consultation::whereMonth('consultation_date', now()->month)
            ->whereYear('consultation_date', now()->year)
            ->count();

        // Consultations by clinician (last 30 days)
        $consultationsByClinician = Consultation::where('consultation_date', '>=', now()->subDays(30))
            ->selectRaw('primary_clinician_id, count(*) as count')
            ->groupBy('primary_clinician_id')
            ->with('primaryClinician:id,first_name,last_name')
            ->get();

        // Top diagnoses (last 90 days)
        $topDiagnoses = \App\Models\Diagnosis::whereHas('consultation', function ($q) {
            $q->where('consultation_date', '>=', now()->subDays(90));
        })
            ->where('is_primary', true)
            ->selectRaw('icd10_code, diagnosis_description, count(*) as count')
            ->groupBy('icd10_code', 'diagnosis_description')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return $this->success([
            'total_patients' => $totalPatients,
            'active_clinicians' => $activeClinicians,
            'new_patients_30_days' => $newPatients30Days,
            'consultations_this_month' => $consultationsThisMonth,
            'consultations_by_clinician' => $consultationsByClinician,
            'top_diagnoses' => $topDiagnoses,
        ], 'Dashboard data retrieved');
    }

    /**
     * Clinician dashboard metrics
     */
    private function clinicianDashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        // Own patients
        $activePatients = Patient::where('created_by', $user->id)
            ->where('is_active', true)
            ->whereHas('consultations', function ($q) {
                $q->where('consultation_date', '>=', now()->subYear());
            })
            ->count();

        // Consultations this month
        $consultationsThisMonth = Consultation::where('primary_clinician_id', $user->id)
            ->whereMonth('consultation_date', now()->month)
            ->whereYear('consultation_date', now()->year)
            ->count();

        // Average daily consultations (last 30 days)
        $dailyConsultations = Consultation::where('primary_clinician_id', $user->id)
            ->where('consultation_date', '>=', now()->subDays(30))
            ->selectRaw('consultation_date, count(*) as count')
            ->groupBy('consultation_date')
            ->get();

        $avgDaily = $dailyConsultations->count() > 0 
            ? round($dailyConsultations->sum('count') / 30, 2) 
            : 0;

        // High-risk patients
        $highRiskPatients = Consultation::where('primary_clinician_id', $user->id)
            ->where('risk_assessment', 'high')
            ->where('consultation_date', '>=', now()->subDays(90))
            ->distinct('patient_id')
            ->count('patient_id');

        // Top diagnoses (last 12 months)
        $topDiagnoses = \App\Models\Diagnosis::whereHas('consultation', function ($q) use ($user) {
            $q->where('primary_clinician_id', $user->id)
                ->where('consultation_date', '>=', now()->subYear());
        })
            ->where('is_primary', true)
            ->selectRaw('icd10_code, diagnosis_description, count(*) as count')
            ->groupBy('icd10_code', 'diagnosis_description')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return $this->success([
            'active_patients' => $activePatients,
            'consultations_this_month' => $consultationsThisMonth,
            'avg_daily_consultations' => $avgDaily,
            'high_risk_patients' => $highRiskPatients,
            'top_diagnoses' => $topDiagnoses,
            'daily_consultations' => $dailyConsultations,
        ], 'Dashboard data retrieved');
    }
}
