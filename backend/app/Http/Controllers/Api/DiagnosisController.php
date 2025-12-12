<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Consultation;
use App\Models\Diagnosis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiagnosisController extends ApiController
{
    /**
     * Get all diagnoses for a consultation
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

        $diagnoses = $consultation->diagnoses()->orderBy('is_primary', 'desc')->get();

        return $this->success($diagnoses);
    }

    /**
     * Store a new diagnosis
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
            'is_primary' => 'required|boolean',
            'icd10_code' => 'required|string|max:10',
            'diagnosis_description' => 'required|string|max:500',
            'diagnosis_status' => 'required|in:provisional,confirmed,rule_out',
            'severity' => 'nullable|in:mild,moderate,severe',
            'onset_date' => 'nullable|date',
            'rationale' => 'nullable|string|max:300',
            'likelihood' => 'nullable|in:high,moderate,low',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        // If this is a primary diagnosis, ensure only one primary exists
        if ($request->is_primary) {
            Diagnosis::where('consultation_id', $consultation->id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        } else {
            // Check limit of 10 differential diagnoses
            $differentialCount = Diagnosis::where('consultation_id', $consultation->id)
                ->where('is_primary', false)
                ->count();
            
            if ($differentialCount >= 10) {
                return $this->error('Maximum of 10 differential diagnoses allowed', [], 422);
            }
        }

        $diagnosis = Diagnosis::create([
            'consultation_id' => $consultation->id,
            'is_primary' => $request->is_primary,
            'icd10_code' => $request->icd10_code,
            'diagnosis_description' => $request->diagnosis_description,
            'diagnosis_status' => $request->diagnosis_status,
            'severity' => $request->severity,
            'onset_date' => $request->onset_date,
            'rationale' => $request->rationale,
            'likelihood' => $request->likelihood,
        ]);

        return $this->success($diagnosis, 'Diagnosis created successfully', 201);
    }

    /**
     * Update a diagnosis
     */
    public function update(Request $request, Consultation $consultation, Diagnosis $diagnosis): JsonResponse
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
            'is_primary' => 'sometimes|required|boolean',
            'icd10_code' => 'sometimes|required|string|max:10',
            'diagnosis_description' => 'sometimes|required|string|max:500',
            'diagnosis_status' => 'sometimes|required|in:provisional,confirmed,rule_out',
            'severity' => 'nullable|in:mild,moderate,severe',
            'onset_date' => 'nullable|date',
            'rationale' => 'nullable|string|max:300',
            'likelihood' => 'nullable|in:high,moderate,low',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        // If changing to primary, unset other primary
        if ($request->has('is_primary') && $request->is_primary && !$diagnosis->is_primary) {
            Diagnosis::where('consultation_id', $consultation->id)
                ->where('id', '!=', $diagnosis->id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }

        $diagnosis->update($request->only([
            'is_primary',
            'icd10_code',
            'diagnosis_description',
            'diagnosis_status',
            'severity',
            'onset_date',
            'rationale',
            'likelihood',
        ]));

        return $this->success($diagnosis, 'Diagnosis updated successfully');
    }

    /**
     * Delete a diagnosis (admin only)
     */
    public function destroy(Request $request, Consultation $consultation, Diagnosis $diagnosis): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
            return $this->error('Access denied', [], 403);
        }

        // Prevent deleting primary diagnosis if it's the only one
        if ($diagnosis->is_primary) {
            $primaryCount = Diagnosis::where('consultation_id', $consultation->id)
                ->where('is_primary', true)
                ->count();
            
            if ($primaryCount <= 1) {
                return $this->error('Cannot delete the only primary diagnosis', [], 422);
            }
        }

        $diagnosis->delete();

        return $this->success(null, 'Diagnosis deleted successfully');
    }
}
