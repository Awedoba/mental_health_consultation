<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Consultation;
use App\Models\MentalStateExam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentalStateExamController extends ApiController
{
    /**
     * Store or update MSE for a consultation
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
            'general_appearance' => 'required|in:well_groomed,disheveled,bizarre,age_appropriate,inappropriately_dressed',
            'level_of_consciousness' => 'required|in:alert,drowsy,lethargic,stuporous,comatose',
            'eye_contact' => 'required|in:appropriate,avoidant,intense,fleeting',
            'psychomotor_activity' => 'required|in:normal,agitated,restless,retarded,hyperactive,catatonic',
            'attitude_toward_examiner' => 'required|in:cooperative,guarded,hostile,suspicious,evasive,seductive',
            'behavior_notes' => 'nullable|string|max:500',
            'speech_rate' => 'required|in:normal,rapid,slow,pressured',
            'speech_volume' => 'required|in:normal,loud,soft,whispered',
            'speech_tone' => 'required|in:normal,monotone,dramatic,flat',
            'speech_fluency' => 'required|in:fluent,non_fluent,hesitant,stuttering',
            'speech_articulation' => 'required|in:clear,slurred,mumbled',
            'speech_notes' => 'nullable|string|max:500',
            'reported_mood' => 'required|string|max:100',
            'observed_affect' => 'required|in:euthymic,depressed,anxious,irritable,euphoric,angry,labile',
            'affect_range' => 'required|in:full,restricted,blunted,flat',
            'affect_congruence' => 'required|in:congruent,incongruent',
            'mood_notes' => 'nullable|string|max:500',
            'thought_organization' => 'required|in:logical,goal_directed,circumstantial,tangential,loose_associations,flight_of_ideas,thought_blocking',
            'thought_coherence' => 'required|in:coherent,incoherent,disorganized',
            'delusions' => 'nullable|array',
            'delusion_details' => 'nullable|string|max:500',
            'obsessions' => 'nullable|boolean',
            'compulsions' => 'nullable|boolean',
            'phobias' => 'nullable|string|max:200',
            'suicidal_ideation' => 'required|in:none,passive,active_no_plan,active_with_plan,active_with_intent',
            'homicidal_ideation' => 'required|in:none,passive,active_no_plan,active_with_plan,active_with_intent',
            'thought_notes' => 'nullable|string|max:1000',
            'hallucinations' => 'nullable|array',
            'hallucination_details' => 'nullable|string|max:500',
            'illusions' => 'nullable|boolean',
            'depersonalization' => 'nullable|boolean',
            'derealization' => 'nullable|boolean',
            'perception_notes' => 'nullable|string|max:500',
            'orientation_person' => 'required|in:oriented,disoriented',
            'orientation_place' => 'required|in:oriented,disoriented',
            'orientation_time' => 'required|in:oriented,disoriented',
            'orientation_situation' => 'required|in:oriented,disoriented',
            'attention_concentration' => 'required|in:intact,impaired,grossly_impaired',
            'memory_immediate' => 'required|in:intact,impaired,grossly_impaired',
            'memory_recent' => 'required|in:intact,impaired,grossly_impaired',
            'memory_remote' => 'required|in:intact,impaired,grossly_impaired',
            'fund_of_knowledge' => 'nullable|in:average,above_average,below_average',
            'abstraction' => 'nullable|in:intact,concrete,impaired',
            'cognition_notes' => 'nullable|string|max:500',
            'insight' => 'required|in:good,fair,poor,absent',
            'insight_description' => 'nullable|string|max:300',
            'judgment' => 'required|in:good,fair,poor,grossly_impaired',
            'judgment_description' => 'nullable|string|max:300',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        // Check if suicidal/homicidal ideation requires safety documentation
        if (($request->suicidal_ideation !== 'none' || $request->homicidal_ideation !== 'none')
            && !$consultation->safety_plan_required) {
            return $this->error('Safety plan is required when suicidal or homicidal ideation is present', [], 422);
        }

        $mse = MentalStateExam::updateOrCreate(
            ['consultation_id' => $consultation->id],
            $request->only([
                'general_appearance',
                'level_of_consciousness',
                'eye_contact',
                'psychomotor_activity',
                'attitude_toward_examiner',
                'behavior_notes',
                'speech_rate',
                'speech_volume',
                'speech_tone',
                'speech_fluency',
                'speech_articulation',
                'speech_notes',
                'reported_mood',
                'observed_affect',
                'affect_range',
                'affect_congruence',
                'mood_notes',
                'thought_organization',
                'thought_coherence',
                'delusions',
                'delusion_details',
                'obsessions',
                'compulsions',
                'phobias',
                'suicidal_ideation',
                'homicidal_ideation',
                'thought_notes',
                'hallucinations',
                'hallucination_details',
                'illusions',
                'depersonalization',
                'derealization',
                'perception_notes',
                'orientation_person',
                'orientation_place',
                'orientation_time',
                'orientation_situation',
                'attention_concentration',
                'memory_immediate',
                'memory_recent',
                'memory_remote',
                'fund_of_knowledge',
                'abstraction',
                'cognition_notes',
                'insight',
                'insight_description',
                'judgment',
                'judgment_description',
            ])
        );

        return $this->success($mse, 'Mental State Examination saved successfully');
    }

    /**
     * Get MSE for a consultation
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

        $mse = $consultation->mentalStateExam;

        if (!$mse) {
            return $this->error('MSE not found for this consultation', [], 404);
        }

        return $this->success($mse);
    }
}
