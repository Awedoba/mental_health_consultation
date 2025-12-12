<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Consultation;
use App\Models\ManagementPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManagementPlanController extends ApiController
{
    /**
     * Get management plan for a consultation
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

        $plan = $consultation->managementPlan;

        if (!$plan) {
            return $this->error('Management plan not found', [], 404);
        }

        return $this->success($plan);
    }

    /**
     * Store or update management plan
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
            'treatment_modalities' => 'required|array',
            'treatment_modalities.*' => 'in:psychotherapy,medication_management,case_management,crisis_intervention,group_therapy,family_therapy,other',
            'psychotherapy_type' => 'nullable|in:cbt,dbt,psychodynamic,supportive,motivational_interviewing,other',
            'therapy_frequency' => 'nullable|in:weekly,biweekly,monthly,as_needed',
            'treatment_goals' => 'required|string|max:2000',
            'clinical_recommendations' => 'required|string|max:2000',
            'patient_education' => 'nullable|string|max:1000',
            'referrals' => 'nullable|string|max:500',
            'next_visit_date' => 'nullable|date',
            'next_visit_purpose' => 'nullable|in:medication_review,therapy_session,progress_check,crisis_followup,discharge_planning',
            'follow_up_interval' => 'nullable|in:1_week,2_weeks,1_month,3_months,6_months,as_needed',
            'urgent_follow_up' => 'nullable|boolean',
            'follow_up_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        // If high risk, require follow-up within 14 days
        if ($consultation->risk_assessment === 'high' && $request->next_visit_date) {
            $followUpDate = \Carbon\Carbon::parse($request->next_visit_date);
            $consultationDate = \Carbon\Carbon::parse($consultation->consultation_date);
            
            if ($followUpDate->diffInDays($consultationDate) > 14) {
                return $this->error('High-risk patients require follow-up within 14 days', [], 422);
            }
        }

        $plan = ManagementPlan::updateOrCreate(
            ['consultation_id' => $consultation->id],
            $request->only([
                'treatment_modalities',
                'psychotherapy_type',
                'therapy_frequency',
                'treatment_goals',
                'clinical_recommendations',
                'patient_education',
                'referrals',
                'next_visit_date',
                'next_visit_purpose',
                'follow_up_interval',
                'urgent_follow_up',
                'follow_up_notes',
            ])
        );

        return $this->success($plan, 'Management plan saved successfully');
    }
}
