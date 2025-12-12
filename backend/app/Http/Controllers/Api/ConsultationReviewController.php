<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Consultation;
use App\Models\ConsultationReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultationReviewController extends ApiController
{
    /**
     * Get all reviews for a consultation
     */
    public function index(Request $request, Consultation $consultation): JsonResponse
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

        $reviews = $consultation->reviews()->with('reviewingClinician')->orderBy('review_date', 'desc')->get();

        return $this->success($reviews);
    }

    /**
     * Store a new review
     */
    public function store(Request $request, Consultation $consultation): JsonResponse
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

        $validator = Validator::make($request->all(), [
            'review_date' => 'required|date|before_or_equal:today',
            'visit_type' => 'required|in:scheduled_followup,unscheduled,crisis_visit,medication_check',
            'bp_systolic' => 'nullable|integer|between:50,250',
            'bp_diastolic' => 'nullable|integer|between:30,150',
            'heart_rate' => 'nullable|integer|between:30,200',
            'respiratory_rate' => 'nullable|integer|between:8,40',
            'temperature' => 'nullable|numeric|between:35,41',
            'weight' => 'nullable|numeric|between:20,250',
            'height' => 'nullable|numeric|between:120,240',
            'vitals_notes' => 'nullable|string|max:300',
            'subjective' => 'required|string|max:2000',
            'objective' => 'required|string|max:2000',
            'treatment_response' => 'required|in:significant_improvement,moderate_improvement,minimal_improvement,no_change,worsening',
            'medication_adherence' => 'nullable|in:fully_adherent,mostly_adherent,partially_adherent,non_adherent,na',
            'therapy_engagement' => 'nullable|in:excellent,good,fair,poor,na',
            'side_effects' => 'nullable|string|max:500',
            'new_symptoms' => 'nullable|string|max:1000',
            'clinical_assessment' => 'required|string|max:2000',
            'plan_changes' => 'nullable|string|max:1500',
            'continue_current_treatment' => 'nullable|boolean',
            'goals_progress' => 'nullable|string|max:1500',
            'new_interventions' => 'nullable|string|max:1000',
            'next_steps' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $review = ConsultationReview::create([
            'patient_id' => $consultation->patient_id,
            'linked_consultation_id' => $consultation->id,
            'reviewing_clinician_id' => $user->id,
            'review_date' => $request->review_date,
            'visit_type' => $request->visit_type,
            'bp_systolic' => $request->bp_systolic,
            'bp_diastolic' => $request->bp_diastolic,
            'heart_rate' => $request->heart_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'temperature' => $request->temperature,
            'weight' => $request->weight,
            'height' => $request->height,
            'vitals_notes' => $request->vitals_notes,
            'subjective' => $request->subjective,
            'objective' => $request->objective,
            'treatment_response' => $request->treatment_response,
            'medication_adherence' => $request->medication_adherence,
            'therapy_engagement' => $request->therapy_engagement,
            'side_effects' => $request->side_effects,
            'new_symptoms' => $request->new_symptoms,
            'clinical_assessment' => $request->clinical_assessment,
            'plan_changes' => $request->plan_changes,
            'continue_current_treatment' => $request->continue_current_treatment ?? false,
            'goals_progress' => $request->goals_progress,
            'new_interventions' => $request->new_interventions,
            'next_steps' => $request->next_steps,
        ]);

        $review->load('reviewingClinician');

        return $this->success($review, 'Consultation review created successfully', 201);
    }

    /**
     * Display the specified review
     */
    public function show(Request $request, Consultation $consultation, ConsultationReview $review): JsonResponse
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

        $review->load('reviewingClinician');

        return $this->success($review);
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Consultation $consultation, ConsultationReview $review): JsonResponse
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

        $validator = Validator::make($request->all(), [
            'review_date' => 'sometimes|required|date|before_or_equal:today',
            'visit_type' => 'sometimes|required|in:scheduled_followup,unscheduled,crisis_visit,medication_check',
            'bp_systolic' => 'nullable|integer|between:50,250',
            'bp_diastolic' => 'nullable|integer|between:30,150',
            'heart_rate' => 'nullable|integer|between:30,200',
            'respiratory_rate' => 'nullable|integer|between:8,40',
            'temperature' => 'nullable|numeric|between:35,41',
            'weight' => 'nullable|numeric|between:20,250',
            'height' => 'nullable|numeric|between:120,240',
            'vitals_notes' => 'nullable|string|max:300',
            'subjective' => 'sometimes|required|string|max:2000',
            'objective' => 'sometimes|required|string|max:2000',
            'treatment_response' => 'sometimes|required|in:significant_improvement,moderate_improvement,minimal_improvement,no_change,worsening',
            'medication_adherence' => 'nullable|in:fully_adherent,mostly_adherent,partially_adherent,non_adherent,na',
            'therapy_engagement' => 'nullable|in:excellent,good,fair,poor,na',
            'side_effects' => 'nullable|string|max:500',
            'new_symptoms' => 'nullable|string|max:1000',
            'clinical_assessment' => 'sometimes|required|string|max:2000',
            'plan_changes' => 'nullable|string|max:1500',
            'continue_current_treatment' => 'nullable|boolean',
            'goals_progress' => 'nullable|string|max:1500',
            'new_interventions' => 'nullable|string|max:1000',
            'next_steps' => 'sometimes|required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $review->update($request->only([
            'review_date',
            'visit_type',
            'bp_systolic',
            'bp_diastolic',
            'heart_rate',
            'respiratory_rate',
            'temperature',
            'weight',
            'height',
            'vitals_notes',
            'subjective',
            'objective',
            'treatment_response',
            'medication_adherence',
            'therapy_engagement',
            'side_effects',
            'new_symptoms',
            'clinical_assessment',
            'plan_changes',
            'continue_current_treatment',
            'goals_progress',
            'new_interventions',
            'next_steps',
        ]));

        $review->load('reviewingClinician');

        return $this->success($review, 'Review updated successfully');
    }

    /**
     * Remove the specified review (admin only)
     */
    public function destroy(Request $request, Consultation $consultation, ConsultationReview $review): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Access denied', [], 403);
        }

        $review->delete();

        return $this->success(null, 'Review deleted successfully');
    }
}
