<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Consultation;
use App\Models\Diagnosis;
use App\Models\MentalStateExam;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends ApiController
{
    /**
     * Patient reports
     */
    public function patients(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Patient::with('createdBy');

        // Role-based filtering
        if ($user->role === 'clinician') {
            $query->where('created_by', $user->id);
        }

        // Apply filters
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $patients = $query->orderBy('created_at', 'desc')->get();

        return $this->success($patients, 'Patient report generated');
    }

    /**
     * Consultation reports
     */
    public function consultations(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Consultation::with(['patient', 'primaryClinician', 'diagnoses' => function ($q) {
            $q->where('is_primary', true);
        }]);

        // Role-based filtering
        if ($user->role === 'clinician') {
            $query->where(function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id)
                    ->orWhereHas('collaborators', function ($subQ) use ($user) {
                        $subQ->where('clinician_id', $user->id);
                    });
            });
        }

        // Apply filters
        if ($request->has('date_from')) {
            $query->where('consultation_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('consultation_date', '<=', $request->date_to);
        }
        if ($request->has('session_type')) {
            $query->where('session_type', $request->session_type);
        }

        $consultations = $query->orderBy('consultation_date', 'desc')->get();

        return $this->success($consultations, 'Consultation report generated');
    }

    /**
     * Diagnosis reports
     */
    public function diagnoses(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Diagnosis::with(['consultation.patient', 'consultation.primaryClinician']);

        // Role-based filtering
        if ($user->role === 'clinician') {
            $query->whereHas('consultation', function ($q) use ($user) {
                $q->where(function ($subQ) use ($user) {
                    $subQ->where('primary_clinician_id', $user->id)
                        ->orWhereHas('collaborators', function ($collabQ) use ($user) {
                            $collabQ->where('clinician_id', $user->id);
                        });
                });
            });
        }

        // Apply filters
        if ($request->has('date_from')) {
            $query->whereHas('consultation', function ($q) use ($request) {
                $q->where('consultation_date', '>=', $request->date_from);
            });
        }
        if ($request->has('date_to')) {
            $query->whereHas('consultation', function ($q) use ($request) {
                $q->where('consultation_date', '<=', $request->date_to);
            });
        }

        $diagnoses = $query->orderBy('created_at', 'desc')->get();

        // Group by diagnosis for summary
        $summary = $diagnoses->groupBy('icd10_code')->map(function ($group) {
            return [
                'icd10_code' => $group->first()->icd10_code,
                'diagnosis_description' => $group->first()->diagnosis_description,
                'count' => $group->count(),
                'unique_patients' => $group->pluck('consultation.patient_id')->unique()->count(),
            ];
        })->values();

        return $this->success([
            'diagnoses' => $diagnoses,
            'summary' => $summary,
        ], 'Diagnosis report generated');
    }

    /**
     * Quality reports
     */
    public function quality(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Consultation::query();

        // Role-based filtering
        if ($user->role === 'clinician') {
            $query->where(function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id)
                    ->orWhereHas('collaborators', function ($subQ) use ($user) {
                        $subQ->where('clinician_id', $user->id);
                    });
            });
        }

        // Apply date filters
        if ($request->has('date_from')) {
            $query->where('consultation_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('consultation_date', '<=', $request->date_to);
        }

        $totalConsultations = $query->count();
        $withMSE = $query->clone()->whereHas('mentalStateExam')->count();
        $withPrimaryDiagnosis = $query->clone()->whereHas('diagnoses', function ($q) {
            $q->where('is_primary', true);
        })->count();
        $withManagementPlan = $query->clone()->whereHas('managementPlan')->count();

        return $this->success([
            'total_consultations' => $totalConsultations,
            'mse_completion_rate' => $totalConsultations > 0 ? round(($withMSE / $totalConsultations) * 100, 2) : 0,
            'diagnosis_completion_rate' => $totalConsultations > 0 ? round(($withPrimaryDiagnosis / $totalConsultations) * 100, 2) : 0,
            'management_plan_completion_rate' => $totalConsultations > 0 ? round(($withManagementPlan / $totalConsultations) * 100, 2) : 0,
        ], 'Quality report generated');
    }
}
