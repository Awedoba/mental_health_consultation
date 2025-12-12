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

        $consultations = $query->orderBy('consultation_date', 'desc')
            ->orderBy('consultation_time', 'desc')
            ->paginate(20);

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
            
            if (!$hasAccess) {
                return $this->error('Access denied', [], 403);
            }
        }

        $consultation->load([
            'patient',
            'primaryClinician',
            'collaborators.clinician',
            'mentalStateExam',
            'diagnoses',
            'managementPlan',
            'reviews.reviewingClinician',
        ]);

        return $this->success($consultation);
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
            
            if (!$hasAccess) {
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
