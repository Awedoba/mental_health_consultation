<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Billing;
use App\Models\Consultation;
use App\Models\ConsultationReview;
use App\Models\Diagnosis;
use App\Models\HomeVisit;
use App\Models\ManagementPlan;
use App\Models\MentalStateExam;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Client List Report - Enhanced with NHIS status
     */
    public function clientList(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Patient::with('createdBy');

        // Role-based filtering
        if ($user->role === 'clinician') {
            $query->where('created_by', $user->id);
        }

        // Apply filters
        if ($request->has('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->name}%")
                    ->orWhere('last_name', 'like', "%{$request->name}%");
            });
        }
        if ($request->has('age_from')) {
            $query->where('age', '>=', $request->age_from);
        }
        if ($request->has('age_to')) {
            $query->where('age', '<=', $request->age_to);
        }
        if ($request->has('sex')) {
            $query->where('sex', $request->sex);
        }
        if ($request->has('nhis_status')) {
            $query->where('nhis_status', $request->nhis_status);
        }
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $patients = $query->orderBy('last_name')->orderBy('first_name')->get();

        return $this->success($patients, 'Client list report generated');
    }

    /**
     * Client List Query - Advanced client search
     */
    public function clientQuery(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Patient::with('createdBy');

        // Role-based filtering
        if ($user->role === 'clinician') {
            $query->where('created_by', $user->id);
        }

        // Advanced filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('patient_id', 'like', "%{$search}%");
            });
        }
        if ($request->has('age_from')) {
            $query->where('age', '>=', $request->age_from);
        }
        if ($request->has('age_to')) {
            $query->where('age', '<=', $request->age_to);
        }
        if ($request->has('sex')) {
            $query->where('sex', $request->sex);
        }
        if ($request->has('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }
        if ($request->has('nhis_status')) {
            $query->where('nhis_status', $request->nhis_status);
        }
        if ($request->has('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }
        if ($request->has('town')) {
            $query->where('town', 'like', "%{$request->town}%");
        }
        if ($request->has('created_from')) {
            $query->where('created_at', '>=', $request->created_from);
        }
        if ($request->has('created_to')) {
            $query->where('created_at', '<=', $request->created_to);
        }
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $patients = $query->orderBy('last_name')->orderBy('first_name')->get();

        return $this->success($patients, 'Client query report generated');
    }

    /**
     * Client Consulting History - All consultations for a patient
     */
    public function consultingHistory(Request $request, string $patientId): JsonResponse
    {
        $user = $request->user();
        
        // Verify patient access
        $patient = Patient::findOrFail($patientId);
        if ($user->role === 'clinician' && $patient->created_by !== $user->id) {
            return $this->error('Access denied', [], 403);
        }

        $query = Consultation::where('patient_id', $patientId)
            ->with([
                'primaryClinician',
                'mentalStateExam',
                'diagnoses',
                'managementPlan',
                'reviews' => function ($q) {
                    $q->orderBy('review_date', 'desc');
                },
            ]);

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('consultation_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('consultation_date', '<=', $request->date_to);
        }

        $consultations = $query->orderBy('consultation_date', 'desc')->get();

        return $this->success([
            'patient' => $patient,
            'consultations' => $consultations,
            'total_consultations' => $consultations->count(),
        ], 'Consulting history report generated');
    }

    /**
     * Next Visit Report - Upcoming scheduled visits
     */
    public function nextVisits(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = now()->toDateString();

        $query = ManagementPlan::with([
            'consultation' => function ($q) {
                $q->with(['patient', 'primaryClinician']);
            },
        ])
            ->whereNotNull('next_visit_date')
            ->where('next_visit_date', '>=', $today);

        // Role-based filtering
        if ($user->role === 'clinician') {
            $query->whereHas('consultation', function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id);
            });
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('next_visit_date', '>=', $request->date_from);
        } else {
            $query->where('next_visit_date', '>=', $today);
        }
        if ($request->has('date_to')) {
            $query->where('next_visit_date', '<=', $request->date_to);
        }

        // Clinician filter
        if ($request->has('clinician_id')) {
            $query->whereHas('consultation', function ($q) use ($request) {
                $q->where('primary_clinician_id', $request->clinician_id);
            });
        }

        $nextVisits = $query->orderBy('next_visit_date', 'asc')->get();

        return $this->success($nextVisits, 'Next visit report generated');
    }

    /**
     * Consulting Data Query - Consultation data with filters
     */
    public function consultingDataQuery(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Consultation::with([
            'patient',
            'primaryClinician',
            'diagnoses',
            'mentalStateExam',
            'managementPlan',
            'reviews',
        ]);

        // Role-based filtering
        if ($user->role === 'clinician') {
            $query->where(function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id)
                    ->orWhereHas('collaborators', function ($subQ) use ($user) {
                        $subQ->where('clinician_id', $user->id);
                    });
            });
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('consultation_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('consultation_date', '<=', $request->date_to);
        }

        // Session type filter
        if ($request->has('session_type')) {
            $query->where('session_type', $request->session_type);
        }

        // Patient filter
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Clinician filter
        if ($request->has('clinician_id')) {
            $query->where('primary_clinician_id', $request->clinician_id);
        }

        // Status filters (based on completion)
        if ($request->has('has_mse')) {
            if ($request->boolean('has_mse')) {
                $query->whereHas('mentalStateExam');
            } else {
                $query->whereDoesntHave('mentalStateExam');
            }
        }
        if ($request->has('has_diagnosis')) {
            if ($request->boolean('has_diagnosis')) {
                $query->whereHas('diagnoses');
            } else {
                $query->whereDoesntHave('diagnoses');
            }
        }
        if ($request->has('has_management_plan')) {
            if ($request->boolean('has_management_plan')) {
                $query->whereHas('managementPlan');
            } else {
                $query->whereDoesntHave('managementPlan');
            }
        }

        $consultations = $query->orderBy('consultation_date', 'desc')->get();

        return $this->success($consultations, 'Consulting data query report generated');
    }

    /**
     * Trend Report - Consultation trends over time
     */
    public function trends(Request $request): JsonResponse
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

        // Date range filter
        $dateFrom = $request->has('date_from') ? $request->date_from : now()->subMonths(6)->toDateString();
        $dateTo = $request->has('date_to') ? $request->date_to : now()->toDateString();
        
        $query->whereBetween('consultation_date', [$dateFrom, $dateTo]);

        // Group by period
        $groupBy = $request->get('group_by', 'month'); // day, week, month, year

        $trends = $query->select(
            DB::raw("DATE_FORMAT(consultation_date, '%Y-%m') as period"),
            DB::raw('COUNT(*) as total_consultations'),
            DB::raw('COUNT(DISTINCT patient_id) as unique_patients')
        )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        // Get weight tracking from reviews
        $weightTrends = ConsultationReview::whereHas('linkedConsultation', function ($q) use ($dateFrom, $dateTo, $user) {
            $q->whereBetween('consultation_date', [$dateFrom, $dateTo]);
            if ($user->role === 'clinician') {
                $q->where('primary_clinician_id', $user->id);
            }
        })
            ->whereNotNull('weight')
            ->select(
                DB::raw("DATE_FORMAT(review_date, '%Y-%m') as period"),
                DB::raw('AVG(weight) as avg_weight'),
                DB::raw('COUNT(*) as weight_measurements')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        // Summary statistics
        $totalConsultations = $query->count();
        $uniquePatients = $query->distinct('patient_id')->count();
        $avgConsultationsPerPatient = $uniquePatients > 0 ? round($totalConsultations / $uniquePatients, 2) : 0;

        return $this->success([
            'trends' => $trends,
            'weight_trends' => $weightTrends,
            'summary' => [
                'total_consultations' => $totalConsultations,
                'unique_patients' => $uniquePatients,
                'avg_consultations_per_patient' => $avgConsultationsPerPatient,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ], 'Trend report generated');
    }

    /**
     * Total Cases Report - Summary statistics
     */
    public function totalCases(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Date range filter
        $dateFrom = $request->has('date_from') ? $request->date_from : now()->startOfYear()->toDateString();
        $dateTo = $request->has('date_to') ? $request->date_to : now()->toDateString();

        // Base queries
        $consultationQuery = Consultation::query();
        $patientQuery = Patient::query();
        $homeVisitQuery = HomeVisit::query();
        $billingQuery = Billing::query();

        // Role-based filtering
        if ($user->role === 'clinician') {
            $consultationQuery->where(function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id)
                    ->orWhereHas('collaborators', function ($subQ) use ($user) {
                        $subQ->where('clinician_id', $user->id);
                    });
            });
            $patientQuery->where('created_by', $user->id);
            $homeVisitQuery->where('clinician_id', $user->id);
        }

        // Apply date filters
        $consultationQuery->whereBetween('consultation_date', [$dateFrom, $dateTo]);
        $patientQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        $homeVisitQuery->whereBetween('visit_date', [$dateFrom, $dateTo]);
        $billingQuery->whereBetween('billing_date', [$dateFrom, $dateTo]);

        // Statistics
        $totalPatients = $patientQuery->count();
        $totalConsultations = $consultationQuery->count();
        $totalHomeVisits = $homeVisitQuery->count();
        $totalBillings = $billingQuery->count();
        
        $totalRevenue = $billingQuery->clone()->where('payment_status', 'paid')->sum('amount');
        $pendingPayments = $billingQuery->clone()->where('payment_status', 'pending')->sum('amount');
        
        $uniquePatientsConsulted = $consultationQuery->clone()->distinct('patient_id')->count();
        $newPatients = $patientQuery->clone()->whereBetween('created_at', [$dateFrom, $dateTo])->count();

        // By session type
        $consultationsByType = $consultationQuery->clone()
            ->select('session_type', DB::raw('COUNT(*) as count'))
            ->groupBy('session_type')
            ->get();

        // By NHIS status
        $patientsByNHIS = $patientQuery->clone()
            ->select('nhis_status', DB::raw('COUNT(*) as count'))
            ->groupBy('nhis_status')
            ->get();

        // By payment status
        $billingsByStatus = $billingQuery->clone()
            ->select('payment_status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_status')
            ->get();

        return $this->success([
            'summary' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'total_patients' => $totalPatients,
                'new_patients' => $newPatients,
                'total_consultations' => $totalConsultations,
                'unique_patients_consulted' => $uniquePatientsConsulted,
                'total_home_visits' => $totalHomeVisits,
                'total_billings' => $totalBillings,
                'total_revenue' => $totalRevenue,
                'pending_payments' => $pendingPayments,
            ],
            'breakdown' => [
                'consultations_by_type' => $consultationsByType,
                'patients_by_nhis' => $patientsByNHIS,
                'billings_by_status' => $billingsByStatus,
            ],
        ], 'Total cases report generated');
    }
}
