<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Billing;
use App\Models\Consultation;
use App\Models\HomeVisit;
use App\Models\ManagementPlan;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends ApiController
{
    /**
     * Get dashboard data based on user role
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get comprehensive stats
        $stats = $this->getStats($user);
        
        // Get recent activities
        $recentConsultations = $this->getRecentConsultations($user);
        $recentBillings = $this->getRecentBillings($user);
        $recentPrescriptions = $this->getRecentPrescriptions($user);
        $recentHomeVisits = $this->getRecentHomeVisits($user);
        $upcomingNextVisits = $this->getUpcomingNextVisits($user);
        
        // Get alerts
        $alerts = $this->getAlerts($user);
        
        // Get chart data
        $charts = $this->getChartData($user);

        return $this->success([
            'stats' => $stats,
            'recentConsultations' => $recentConsultations,
            'recentBillings' => $recentBillings,
            'recentPrescriptions' => $recentPrescriptions,
            'recentHomeVisits' => $recentHomeVisits,
            'upcomingNextVisits' => $upcomingNextVisits,
            'alerts' => $alerts,
            'charts' => $charts,
        ], 'Dashboard data retrieved');
    }

    /**
     * Get statistics based on user role
     */
    private function getStats($user): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        if ($user->role === 'admin') {
            // Admin sees all data
            $patientQuery = Patient::where('is_active', true);
            $consultationQuery = Consultation::query();
            $homeVisitQuery = HomeVisit::query();
            $billingQuery = Billing::query();
            $prescriptionQuery = Prescription::query();
        } else {
            // Clinician sees only their data
            $patientQuery = Patient::whereHas('consultations', function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id);
            })->where('is_active', true);
            
            $consultationQuery = Consultation::where('primary_clinician_id', $user->id);
            $homeVisitQuery = HomeVisit::where('clinician_id', $user->id);
            $billingQuery = Billing::where('created_by', $user->id);
            $prescriptionQuery = Prescription::where('prescribed_by', $user->id);
        }

        // Base stats
        $stats = [
            'totalPatients' => $patientQuery->count(),
            'insuredPatients' => (clone $patientQuery)->where('nhis_status', 'insured')->count(),
            'uninsuredPatients' => (clone $patientQuery)->where('nhis_status', 'uninsured')->count(),
            'consultationsThisMonth' => (clone $consultationQuery)
                ->whereBetween('consultation_date', [$startOfMonth, $endOfMonth])
                ->count(),
            'homeVisitsThisMonth' => (clone $homeVisitQuery)
                ->whereBetween('visit_date', [$startOfMonth, $endOfMonth])
                ->count(),
            'activePrescriptions' => (clone $prescriptionQuery)->where('is_active', true)->count(),
            'totalPrescriptionsThisMonth' => (clone $prescriptionQuery)
                ->whereBetween('prescription_date', [$startOfMonth, $endOfMonth])
                ->count(),
            'totalRevenueThisMonth' => (clone $billingQuery)
                ->whereBetween('billing_date', [$startOfMonth, $endOfMonth])
                ->where('payment_status', 'paid')
                ->sum('amount') ?? 0,
            'totalRevenueAllTime' => (clone $billingQuery)
                ->where('payment_status', 'paid')
                ->sum('amount') ?? 0,
            'pendingPaymentsAmount' => (clone $billingQuery)
                ->where('payment_status', 'pending')
                ->sum('amount') ?? 0,
            'upcomingNextVisits' => ManagementPlan::whereHas('consultation', function ($q) use ($user) {
                if ($user->role !== 'admin') {
                    $q->where('primary_clinician_id', $user->id);
                }
            })
                ->whereNotNull('next_visit_date')
                ->where('next_visit_date', '>=', $now->toDateString())
                ->where('next_visit_date', '<=', $now->copy()->addDays(30)->toDateString())
                ->count(),
            'totalBillingsThisMonth' => (clone $billingQuery)
                ->whereBetween('billing_date', [$startOfMonth, $endOfMonth])
                ->count(),
        ];

        // Admin-only stats
        if ($user->role === 'admin') {
            $stats['activeClinicians'] = User::where('role', 'clinician')
                ->where('is_active', true)
                ->count();
        }

        // Clinician-specific stats
        if ($user->role === 'clinician') {
            $stats['myConsultationsThisMonth'] = Consultation::where('primary_clinician_id', $user->id)
                ->whereBetween('consultation_date', [$startOfMonth, $endOfMonth])
                ->count();
            $stats['myHomeVisitsThisMonth'] = HomeVisit::where('clinician_id', $user->id)
                ->whereBetween('visit_date', [$startOfMonth, $endOfMonth])
                ->count();
            $stats['myActivePrescriptions'] = Prescription::where('prescribed_by', $user->id)
                ->where('is_active', true)
                ->count();
            $stats['myPendingBillings'] = Billing::where('created_by', $user->id)
                ->where('payment_status', 'pending')
                ->count();
        }

        return $stats;
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

    /**
     * Get recent billings
     */
    private function getRecentBillings($user): array
    {
        $query = Billing::with(['patient', 'consultation'])
            ->orderBy('billing_date', 'desc')
            ->limit(10);

        if ($user->role !== 'admin') {
            $query->where('created_by', $user->id);
        }

        return $query->get()->map(function ($billing) {
            return [
                'id' => $billing->id,
                'patient_id' => $billing->patient_id,
                'patient_name' => $billing->patient 
                    ? $billing->patient->first_name . ' ' . $billing->patient->last_name
                    : 'N/A',
                'amount' => $billing->amount,
                'payment_status' => $billing->payment_status,
                'billing_date' => $billing->billing_date,
                'invoice_number' => $billing->invoice_number,
                'service_type' => $billing->service_type,
            ];
        })->toArray();
    }

    /**
     * Get recent prescriptions
     */
    private function getRecentPrescriptions($user): array
    {
        $query = Prescription::with(['patient', 'medication', 'prescribedBy'])
            ->orderBy('prescription_date', 'desc')
            ->limit(10);

        if ($user->role !== 'admin') {
            $query->where('prescribed_by', $user->id);
        }

        return $query->get()->map(function ($prescription) {
            return [
                'id' => $prescription->id,
                'patient_id' => $prescription->patient_id,
                'patient_name' => $prescription->patient 
                    ? $prescription->patient->first_name . ' ' . $prescription->patient->last_name
                    : 'N/A',
                'medication_name' => $prescription->medication ? $prescription->medication->name : 'N/A',
                'prescribed_by_name' => $prescription->prescribedBy
                    ? $prescription->prescribedBy->first_name . ' ' . $prescription->prescribedBy->last_name
                    : 'N/A',
                'prescription_date' => $prescription->prescription_date,
                'is_active' => $prescription->is_active,
                'dosage' => $prescription->dosage,
                'frequency' => $prescription->frequency,
            ];
        })->toArray();
    }

    /**
     * Get recent home visits
     */
    private function getRecentHomeVisits($user): array
    {
        $query = HomeVisit::with(['patient', 'clinician'])
            ->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->limit(10);

        if ($user->role !== 'admin') {
            $query->where('clinician_id', $user->id);
        }

        return $query->get()->map(function ($visit) {
            return [
                'id' => $visit->id,
                'patient_id' => $visit->patient_id,
                'client_name' => $visit->client_name,
                'patient_name' => $visit->patient 
                    ? $visit->patient->first_name . ' ' . $visit->patient->last_name
                    : null,
                'community_location' => $visit->community_location,
                'visit_date' => $visit->visit_date,
                'visit_time' => $visit->visit_time,
                'clinician_name' => $visit->clinician
                    ? $visit->clinician->first_name . ' ' . $visit->clinician->last_name
                    : 'N/A',
            ];
        })->toArray();
    }

    /**
     * Get upcoming next visits
     */
    private function getUpcomingNextVisits($user): array
    {
        $query = ManagementPlan::with(['consultation.patient', 'consultation.primaryClinician'])
            ->whereNotNull('next_visit_date')
            ->where('next_visit_date', '>=', now()->toDateString())
            ->orderBy('next_visit_date', 'asc')
            ->limit(10);

        if ($user->role !== 'admin') {
            $query->whereHas('consultation', function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id);
            });
        }

        return $query->get()->map(function ($plan) {
            return [
                'id' => $plan->id,
                'consultation_id' => $plan->consultation_id,
                'next_visit_date' => $plan->next_visit_date,
                'next_visit_purpose' => $plan->next_visit_purpose,
                'patient_name' => $plan->consultation && $plan->consultation->patient
                    ? $plan->consultation->patient->first_name . ' ' . $plan->consultation->patient->last_name
                    : 'N/A',
                'clinician_name' => $plan->consultation && $plan->consultation->primaryClinician
                    ? $plan->consultation->primaryClinician->first_name . ' ' . $plan->consultation->primaryClinician->last_name
                    : 'N/A',
            ];
        })->toArray();
    }

    /**
     * Get alerts
     */
    private function getAlerts($user): array
    {
        $billingQuery = Billing::query();
        $prescriptionQuery = Prescription::query();
        $managementPlanQuery = ManagementPlan::query();
        $consultationQuery = Consultation::query();

        if ($user->role !== 'admin') {
            $billingQuery->where('created_by', $user->id);
            $prescriptionQuery->where('prescribed_by', $user->id);
            $managementPlanQuery->whereHas('consultation', function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id);
            });
            $consultationQuery->where('primary_clinician_id', $user->id);
        }

        // Overdue payments (pending payments older than 7 days)
        $overduePayments = $billingQuery->clone()
            ->where('payment_status', 'pending')
            ->where('billing_date', '<', now()->subDays(7)->toDateString())
            ->with('patient')
            ->limit(5)
            ->get()
            ->map(function ($billing) {
                return [
                    'id' => $billing->id,
                    'patient_name' => $billing->patient 
                        ? $billing->patient->first_name . ' ' . $billing->patient->last_name
                        : 'N/A',
                    'amount' => $billing->amount,
                    'billing_date' => $billing->billing_date,
                    'invoice_number' => $billing->invoice_number,
                ];
            })->toArray();

        // Upcoming visits (next 7 days)
        $upcomingVisits = $managementPlanQuery->clone()
            ->whereNotNull('next_visit_date')
            ->whereBetween('next_visit_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->with(['consultation.patient', 'consultation.primaryClinician'])
            ->limit(5)
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'consultation_id' => $plan->consultation_id,
                    'next_visit_date' => $plan->next_visit_date,
                    'patient_name' => $plan->consultation && $plan->consultation->patient
                        ? $plan->consultation->patient->first_name . ' ' . $plan->consultation->patient->last_name
                        : 'N/A',
                ];
            })->toArray();

        // Expiring prescriptions (within 7 days)
        $expiringPrescriptions = $prescriptionQuery->clone()
            ->where('is_active', true)
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->with(['patient', 'medication'])
            ->limit(5)
            ->get()
            ->map(function ($prescription) {
                return [
                    'id' => $prescription->id,
                    'patient_name' => $prescription->patient 
                        ? $prescription->patient->first_name . ' ' . $prescription->patient->last_name
                        : 'N/A',
                    'medication_name' => $prescription->medication ? $prescription->medication->name : 'N/A',
                    'end_date' => $prescription->end_date,
                ];
            })->toArray();

        // Incomplete consultations (missing MSE or management plan)
        $incompleteConsultations = $consultationQuery->clone()
            ->where('is_locked', false)
            ->where(function ($q) {
                $q->whereDoesntHave('mentalStateExam')
                    ->orWhereDoesntHave('managementPlan');
            })
            ->with('patient')
            ->limit(5)
            ->get()
            ->map(function ($consultation) {
                return [
                    'id' => $consultation->id,
                    'patient_name' => $consultation->patient 
                        ? $consultation->patient->first_name . ' ' . $consultation->patient->last_name
                        : 'N/A',
                    'consultation_date' => $consultation->consultation_date,
                    'missing_mse' => !$consultation->mentalStateExam,
                    'missing_plan' => !$consultation->managementPlan,
                ];
            })->toArray();

        return [
            'overduePayments' => $overduePayments,
            'upcomingVisits' => $upcomingVisits,
            'expiringPrescriptions' => $expiringPrescriptions,
            'incompleteConsultations' => $incompleteConsultations,
        ];
    }

    /**
     * Get chart data
     */
    private function getChartData($user): array
    {
        $months = [];
        $consultations = [];
        $homeVisits = [];
        $prescriptions = [];
        $revenue = [];
        $paid = [];
        $pending = [];

        // Get last 6 months data
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $months[] = $date->format('M Y');

            $consultationQuery = Consultation::whereBetween('consultation_date', [$startOfMonth, $endOfMonth]);
            $homeVisitQuery = HomeVisit::whereBetween('visit_date', [$startOfMonth, $endOfMonth]);
            $prescriptionQuery = Prescription::whereBetween('prescription_date', [$startOfMonth, $endOfMonth]);
            $billingQuery = Billing::whereBetween('billing_date', [$startOfMonth, $endOfMonth]);

            if ($user->role !== 'admin') {
                $consultationQuery->where('primary_clinician_id', $user->id);
                $homeVisitQuery->where('clinician_id', $user->id);
                $prescriptionQuery->where('prescribed_by', $user->id);
                $billingQuery->where('created_by', $user->id);
            }

            $consultations[] = $consultationQuery->count();
            $homeVisits[] = $homeVisitQuery->count();
            $prescriptions[] = $prescriptionQuery->count();
            
            $totalRevenue = $billingQuery->clone()->where('payment_status', 'paid')->sum('amount') ?? 0;
            $totalPaid = $totalRevenue;
            $totalPending = $billingQuery->clone()->where('payment_status', 'pending')->sum('amount') ?? 0;
            
            $revenue[] = $totalRevenue;
            $paid[] = $totalPaid;
            $pending[] = $totalPending;
        }

        // NHIS coverage
        $patientQuery = Patient::where('is_active', true);
        if ($user->role !== 'admin') {
            $patientQuery->whereHas('consultations', function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id);
            });
        }
        $totalPatients = $patientQuery->count();
        $insuredPatients = (clone $patientQuery)->where('nhis_status', 'insured')->count();
        $uninsuredPatients = $totalPatients - $insuredPatients;

        // Payment status distribution
        $billingQuery = Billing::query();
        if ($user->role !== 'admin') {
            $billingQuery->where('created_by', $user->id);
        }
        $totalBillings = $billingQuery->count();
        $paidCount = (clone $billingQuery)->where('payment_status', 'paid')->count();
        $pendingCount = (clone $billingQuery)->where('payment_status', 'pending')->count();
        $partialCount = (clone $billingQuery)->where('payment_status', 'partial')->count();
        $waivedCount = (clone $billingQuery)->where('payment_status', 'waived')->count();

        return [
            'serviceActivity' => [
                'labels' => $months,
                'consultations' => $consultations,
                'homeVisits' => $homeVisits,
                'prescriptions' => $prescriptions,
            ],
            'revenue' => [
                'labels' => $months,
                'totalRevenue' => $revenue,
                'paid' => $paid,
                'pending' => $pending,
            ],
            'nhisCoverage' => [
                'insured' => $totalPatients > 0 ? round(($insuredPatients / $totalPatients) * 100, 1) : 0,
                'uninsured' => $totalPatients > 0 ? round(($uninsuredPatients / $totalPatients) * 100, 1) : 0,
            ],
            'paymentStatus' => [
                'paid' => $totalBillings > 0 ? round(($paidCount / $totalBillings) * 100, 1) : 0,
                'pending' => $totalBillings > 0 ? round(($pendingCount / $totalBillings) * 100, 1) : 0,
                'partial' => $totalBillings > 0 ? round(($partialCount / $totalBillings) * 100, 1) : 0,
                'waived' => $totalBillings > 0 ? round(($waivedCount / $totalBillings) * 100, 1) : 0,
            ],
        ];
    }
}
