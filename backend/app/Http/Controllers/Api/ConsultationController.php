<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Consultation;
use App\Models\ConsultationCollaborator;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends ApiController
{
    /**
     * Display a listing of consultations
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Consultation::with(['patient', 'primaryClinician', 'collaborators.clinician']);

        // Role-based filtering
        if ($user->role === 'clinician') {
            $query->where(function ($q) use ($user) {
                $q->where('primary_clinician_id', $user->id)
                    ->orWhereHas('collaborators', function ($subQ) use ($user) {
                        $subQ->where('clinician_id', $user->id);
                    });
            });
        }

        // Filters
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->has('date_from')) {
            $query->where('consultation_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('consultation_date', '<=', $request->date_to);
        }

        if ($request->has('session_type')) {
            $query->where('session_type', $request->session_type);
        }

        // Status filter (locked/unlocked)
        if ($request->has('status')) {
            if ($request->status === 'locked') {
                $query->where('is_locked', true);
            } elseif ($request->status === 'unlocked') {
                $query->where('is_locked', false);
            }
        }

        // Risk assessment filter
        if ($request->has('risk_assessment')) {
            $query->where('risk_assessment', $request->risk_assessment);
        }

        // Search filter (patient or clinician name)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('primaryClinician', function ($clinicianQuery) use ($search) {
                    $clinicianQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        $consultations = $query->orderBy('consultation_date', 'desc')
            ->orderBy('consultation_time', 'desc')
            ->paginate(20);

        // Add computed fields for each consultation
        $consultations->getCollection()->transform(function ($consultation) {
            $patientName = 'N/A';
            if ($consultation->patient) {
                $patientName = trim(($consultation->patient->first_name ?? '').' '.($consultation->patient->last_name ?? ''));
                if (empty($patientName)) {
                    $patientName = 'N/A';
                }
            }

            $clinicianName = 'N/A';
            if ($consultation->primaryClinician) {
                $clinicianName = trim(($consultation->primaryClinician->first_name ?? '').' '.($consultation->primaryClinician->last_name ?? ''));
                if (empty($clinicianName)) {
                    $clinicianName = 'N/A';
                }
            }

            $consultation->patient_name = $patientName;
            $consultation->clinician_name = $clinicianName;

            return $consultation;
        });

        return $this->paginated($consultations, 'Consultations retrieved successfully');
    }

    /**
     * Store a newly created consultation
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'consultation_date' => 'required|date|before_or_equal:today',
            'consultation_time' => 'required|date_format:H:i',
            'session_type' => 'required|in:initial_assessment,follow_up,crisis_intervention,therapy_session,medication_review',
            'session_duration' => 'nullable|integer|min:1|max:480',
            'chief_complaint' => 'required|string|max:500',
            'history_present_illness' => 'required|string|max:5000',
            'past_psychiatric_history' => 'nullable|string|max:3000',
            'medical_history' => 'nullable|string|max:2000',
            'family_history' => 'nullable|string|max:2000',
            'social_history' => 'nullable|string|max:2000',
            'current_medications' => 'nullable|string|max:1500',
            'allergies' => 'nullable|string|max:500',
            'risk_assessment' => 'required|in:low,moderate,high',
            'safety_plan_required' => 'nullable|boolean',
            'clinical_summary' => 'required|string|max:3000',
            'treatment_interventions' => 'nullable|string|max:2000',
            'clinical_notes' => 'nullable|string|max:5000',
            'collaborating_clinicians' => 'nullable|array',
            'collaborating_clinicians.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        // Check for multiple consultations same patient/day (warning only)
        $sameDayConsultation = Consultation::where('patient_id', $request->patient_id)
            ->where('consultation_date', $request->consultation_date)
            ->first();

        if ($sameDayConsultation) {
            // Warning but allow creation
        }

        $consultation = Consultation::create([
            'patient_id' => $request->patient_id,
            'primary_clinician_id' => $request->user()->id,
            'consultation_date' => $request->consultation_date,
            'consultation_time' => $request->consultation_time,
            'session_type' => $request->session_type,
            'session_duration' => $request->session_duration,
            'chief_complaint' => $request->chief_complaint,
            'history_present_illness' => $request->history_present_illness,
            'past_psychiatric_history' => $request->past_psychiatric_history,
            'medical_history' => $request->medical_history,
            'family_history' => $request->family_history,
            'social_history' => $request->social_history,
            'current_medications' => $request->current_medications,
            'allergies' => $request->allergies,
            'risk_assessment' => $request->risk_assessment,
            'safety_plan_required' => $request->safety_plan_required ?? false,
            'clinical_summary' => $request->clinical_summary,
            'treatment_interventions' => $request->treatment_interventions,
            'clinical_notes' => $request->clinical_notes,
            'is_locked' => false,
        ]);

        // Add collaborating clinicians
        if ($request->has('collaborating_clinicians')) {
            foreach ($request->collaborating_clinicians as $clinicianId) {
                if ($clinicianId !== $request->user()->id) {
                    ConsultationCollaborator::create([
                        'consultation_id' => $consultation->id,
                        'clinician_id' => $clinicianId,
                        'added_by' => $request->user()->id,
                    ]);
                }
            }
        }

        $consultation->load(['patient', 'primaryClinician', 'collaborators.clinician']);

        return $this->success($consultation, 'Consultation created successfully', 201);
    }

    /**
     * Display the specified consultation
     */
    public function show(Request $request, Consultation $consultation): JsonResponse
    {
        $user = $request->user();

        // Check access
        if ($user->role === 'clinician') {
            $hasAccess = $consultation->primary_clinician_id === $user->id
                || $consultation->collaborators()->where('clinician_id', $user->id)->exists();

            if (! $hasAccess) {
                return $this->error('Access denied', [], 403);
            }
        }

        // Load required relationships
        $consultation->load([
            'patient',
            'primaryClinician',
            'collaborators.clinician',
        ]);

        // Load optional relationships - these may not exist if tables haven't been migrated
        try {
            $consultation->load(['mentalStateExam', 'diagnoses', 'managementPlan', 'reviews.reviewingClinician']);
        } catch (\Exception $e) {
            // Silently ignore if optional relationships fail (tables may not exist yet)
        }

        // Add computed fields for frontend compatibility
        $patientName = 'N/A';
        if ($consultation->patient) {
            $patientName = trim(($consultation->patient->first_name ?? '').' '.($consultation->patient->last_name ?? ''));
            if (empty($patientName)) {
                $patientName = 'N/A';
            }
        }

        $clinicianName = 'N/A';
        if ($consultation->primaryClinician) {
            $clinicianName = trim(($consultation->primaryClinician->first_name ?? '').' '.($consultation->primaryClinician->last_name ?? ''));
            if (empty($clinicianName)) {
                $clinicianName = 'N/A';
            }
        }

        // Convert to array and add computed fields
        $consultationData = $consultation->toArray();
        $consultationData['patient_name'] = $patientName;
        $consultationData['clinician_name'] = $clinicianName;

        // Format date for form input (YYYY-MM-DD)
        if ($consultation->consultation_date) {
            $consultationData['consultation_date'] = $consultation->consultation_date->format('Y-m-d');
        }

        // Format time for form input (HH:MM)
        if ($consultation->consultation_time) {
            $consultationData['consultation_time'] = $consultation->consultation_time->format('H:i');
        }

        // Add relationship data as flat fields for frontend compatibility
        // Mental state exam is a complex object - frontend can access via mental_state_exam relationship
        // For now, we'll leave it as null if not needed in the simple edit form

        // Get primary diagnosis if exists (for simple display)
        if ($consultation->diagnoses && $consultation->diagnoses->isNotEmpty()) {
            $primaryDiagnosis = $consultation->diagnoses->where('is_primary', true)->first()
                ?? $consultation->diagnoses->first();
            $consultationData['diagnosis'] = ($primaryDiagnosis->icd10_code ?? '').' - '.($primaryDiagnosis->diagnosis_description ?? '');
        }

        // Get treatment plan from management plan if exists
        if ($consultation->managementPlan) {
            $plan = $consultation->managementPlan;
            $treatmentPlanParts = [];
            if ($plan->treatment_goals) {
                $treatmentPlanParts[] = 'Goals: '.$plan->treatment_goals;
            }
            if ($plan->clinical_recommendations) {
                $treatmentPlanParts[] = 'Recommendations: '.$plan->clinical_recommendations;
            }
            if ($plan->follow_up_notes) {
                $treatmentPlanParts[] = 'Follow-up: '.$plan->follow_up_notes;
            }
            $consultationData['treatment_plan'] = implode("\n\n", $treatmentPlanParts);
        }

        return $this->success($consultationData);
    }

    /**
     * Update the specified consultation
     */
    public function update(Request $request, Consultation $consultation): JsonResponse
    {
        $user = $request->user();

        // Check access and lock status
        if ($user->role === 'clinician') {
            $hasAccess = $consultation->primary_clinician_id === $user->id
                || $consultation->collaborators()->where('clinician_id', $user->id)->exists();

            if (! $hasAccess) {
                return $this->error('Access denied', [], 403);
            }
        }

        // Check if consultation is locked (30 days old)
        if ($consultation->is_locked && $user->role !== 'admin') {
            return $this->error('Consultation is locked and cannot be modified', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'consultation_date' => 'sometimes|required|date|before_or_equal:today',
            'consultation_time' => 'sometimes|required|date_format:H:i',
            'session_type' => 'sometimes|required|in:initial_assessment,follow_up,crisis_intervention,therapy_session,medication_review',
            'session_duration' => 'nullable|integer|min:1|max:480',
            'chief_complaint' => 'sometimes|required|string|max:500',
            'history_present_illness' => 'sometimes|required|string|max:5000',
            'past_psychiatric_history' => 'nullable|string|max:3000',
            'medical_history' => 'nullable|string|max:2000',
            'family_history' => 'nullable|string|max:2000',
            'social_history' => 'nullable|string|max:2000',
            'current_medications' => 'nullable|string|max:1500',
            'allergies' => 'nullable|string|max:500',
            'risk_assessment' => 'sometimes|required|in:low,moderate,high',
            'safety_plan_required' => 'nullable|boolean',
            'clinical_summary' => 'sometimes|required|string|max:3000',
            'treatment_interventions' => 'nullable|string|max:2000',
            'clinical_notes' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $consultation->update($request->only([
            'consultation_date',
            'consultation_time',
            'session_type',
            'session_duration',
            'chief_complaint',
            'history_present_illness',
            'past_psychiatric_history',
            'medical_history',
            'family_history',
            'social_history',
            'current_medications',
            'allergies',
            'risk_assessment',
            'safety_plan_required',
            'clinical_summary',
            'treatment_interventions',
            'clinical_notes',
        ]));

        $consultation->load(['patient', 'primaryClinician', 'collaborators.clinician']);

        return $this->success($consultation, 'Consultation updated successfully');
    }

    /**
     * Remove the specified consultation (admin only)
     */
    public function destroy(Request $request, Consultation $consultation): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Access denied. Only administrators can delete consultations.', [], 403);
        }

        $consultation->delete();

        return $this->success(null, 'Consultation deleted successfully');
    }
}
