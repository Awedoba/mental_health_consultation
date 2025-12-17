<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\HomeVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeVisitController extends ApiController
{
    /**
     * Display a listing of home visits
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = HomeVisit::with(['patient', 'clinician']);

        // Role-based filtering
        if ($user->role === 'clinician') {
            // Clinicians see only their own home visits
            $query->where('clinician_id', $user->id);
        }
        // Admins see all home visits

        // Filter by patient
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by clinician
        if ($request->has('clinician_id')) {
            $query->where('clinician_id', $request->clinician_id);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('visit_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('visit_date', '<=', $request->date_to);
        }

        // Search by client name, community, or contact
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhere('community_location', 'like', "%{$search}%")
                    ->orWhere('contact', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $homeVisits = $query->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->paginate(20);

        return $this->paginated($homeVisits, 'Home visits retrieved successfully');
    }

    /**
     * Store a newly created home visit
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'nullable|exists:patients,id',
            'client_name' => 'required|string|max:200',
            'age' => 'nullable|integer|min:0|max:150',
            'sex' => 'nullable|in:male,female,other',
            'community_location' => 'required|string|max:200',
            'contact' => 'required|string|max:20',
            'visit_date' => 'required|date',
            'visit_time' => 'nullable|date_format:H:i',
            'diagnosis_condition' => 'nullable|string|max:2000',
            'medication_prescription' => 'nullable|string|max:2000',
            'observations' => 'nullable|string|max:2000',
            'impression' => 'nullable|string|max:2000',
            'management' => 'nullable|string|max:2000',
            'recommendation' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $homeVisit = HomeVisit::create([
            'patient_id' => $request->patient_id,
            'client_name' => $request->client_name,
            'age' => $request->age,
            'sex' => $request->sex,
            'community_location' => $request->community_location,
            'contact' => $request->contact,
            'visit_date' => $request->visit_date,
            'visit_time' => $request->visit_time,
            'clinician_id' => $request->user()->id,
            'diagnosis_condition' => $request->diagnosis_condition,
            'medication_prescription' => $request->medication_prescription,
            'observations' => $request->observations,
            'impression' => $request->impression,
            'management' => $request->management,
            'recommendation' => $request->recommendation,
        ]);

        $homeVisit->load(['patient', 'clinician']);

        return $this->success($homeVisit, 'Home visit created successfully', 201);
    }

    /**
     * Display the specified home visit
     */
    public function show(Request $request, HomeVisit $homeVisit): JsonResponse
    {
        $user = $request->user();

        // Check access
        if ($user->role === 'clinician' && $homeVisit->clinician_id !== $user->id) {
            return $this->error('Access denied', [], 403);
        }

        $homeVisit->load(['patient', 'clinician']);

        return $this->success($homeVisit);
    }

    /**
     * Update the specified home visit
     */
    public function update(Request $request, HomeVisit $homeVisit): JsonResponse
    {
        $user = $request->user();

        // Check access
        if ($user->role === 'clinician' && $homeVisit->clinician_id !== $user->id) {
            return $this->error('Access denied', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'patient_id' => 'nullable|exists:patients,id',
            'client_name' => 'sometimes|required|string|max:200',
            'age' => 'nullable|integer|min:0|max:150',
            'sex' => 'nullable|in:male,female,other',
            'community_location' => 'sometimes|required|string|max:200',
            'contact' => 'sometimes|required|string|max:20',
            'visit_date' => 'sometimes|required|date',
            'visit_time' => 'nullable|date_format:H:i',
            'diagnosis_condition' => 'nullable|string|max:2000',
            'medication_prescription' => 'nullable|string|max:2000',
            'observations' => 'nullable|string|max:2000',
            'impression' => 'nullable|string|max:2000',
            'management' => 'nullable|string|max:2000',
            'recommendation' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $homeVisit->update($request->only([
            'patient_id',
            'client_name',
            'age',
            'sex',
            'community_location',
            'contact',
            'visit_date',
            'visit_time',
            'diagnosis_condition',
            'medication_prescription',
            'observations',
            'impression',
            'management',
            'recommendation',
        ]));

        $homeVisit->load(['patient', 'clinician']);

        return $this->success($homeVisit, 'Home visit updated successfully');
    }

    /**
     * Remove the specified home visit (admin only)
     */
    public function destroy(Request $request, HomeVisit $homeVisit): JsonResponse
    {
        // Only admins can delete
        if ($request->user()->role !== 'admin') {
            return $this->error('Access denied. Only administrators can delete home visits.', [], 403);
        }

        $homeVisit->delete();

        return $this->success(null, 'Home visit deleted successfully');
    }

    /**
     * Get home visits for a specific patient
     */
    public function getByPatient(Request $request, string $patientId): JsonResponse
    {
        $user = $request->user();
        $query = HomeVisit::where('patient_id', $patientId)
            ->with(['clinician']);

        // Check access
        if ($user->role === 'clinician') {
            $patient = \App\Models\Patient::find($patientId);
            if (!$patient || $patient->created_by !== $user->id) {
                return $this->error('Access denied', [], 403);
            }
        }

        $homeVisits = $query->orderBy('visit_date', 'desc')->get();

        return $this->success($homeVisits, 'Patient home visits retrieved successfully');
    }
}
