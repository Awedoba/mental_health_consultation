<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrescriptionController extends ApiController
{
    /**
     * Display a listing of prescriptions
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Prescription::with(['patient', 'consultation', 'prescribedBy', 'medication']);

        // Role-based filtering
        if ($user->role === 'clinician') {
            // Clinicians see prescriptions for their patients or their own prescriptions
            $query->where(function ($q) use ($user) {
                $q->whereHas('patient', function ($patientQuery) use ($user) {
                    $patientQuery->where('created_by', $user->id);
                })->orWhere('prescribed_by', $user->id);
            });
        }
        // Admins see all prescriptions

        // Filter by patient
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by consultation
        if ($request->has('consultation_id')) {
            $query->where('consultation_id', $request->consultation_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        } else {
            // Default to active only
            $query->where('is_active', true);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('prescription_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('prescription_date', '<=', $request->date_to);
        }

        // Search by medication name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('medication', function ($medicationQuery) use ($search) {
                $medicationQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%");
            });
        }

        $prescriptions = $query->orderBy('prescription_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->paginated($prescriptions, 'Prescriptions retrieved successfully');
    }

    /**
     * Store a newly created prescription
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'prescription_date' => 'required|date',
            'medication_id' => 'required|exists:medications,id',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration' => 'required|string|max:100',
            'quantity' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string|max:1000',
            'refills' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $prescription = Prescription::create([
            'patient_id' => $request->patient_id,
            'consultation_id' => $request->consultation_id,
            'prescription_date' => $request->prescription_date,
            'prescribed_by' => $request->user()->id,
            'medication_id' => $request->medication_id,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
            'quantity' => $request->quantity,
            'instructions' => $request->instructions,
            'refills' => $request->refills ?? 0,
            'is_active' => $request->is_active ?? true,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        $prescription->load(['patient', 'consultation', 'prescribedBy', 'medication']);

        return $this->success($prescription, 'Prescription created successfully', 201);
    }

    /**
     * Display the specified prescription
     */
    public function show(Request $request, Prescription $prescription): JsonResponse
    {
        $user = $request->user();

        // Check access
        if ($user->role === 'clinician') {
            $hasAccess = $prescription->patient->created_by === $user->id
                || $prescription->prescribed_by === $user->id;

            if (! $hasAccess) {
                return $this->error('Access denied', [], 403);
            }
        }

        $prescription->load(['patient', 'consultation', 'prescribedBy', 'medication']);

        return $this->success($prescription);
    }

    /**
     * Update the specified prescription
     */
    public function update(Request $request, Prescription $prescription): JsonResponse
    {
        $user = $request->user();

        // Check access
        if ($user->role === 'clinician') {
            $hasAccess = $prescription->patient->created_by === $user->id
                || $prescription->prescribed_by === $user->id;

            if (! $hasAccess) {
                return $this->error('Access denied', [], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'prescription_date' => 'sometimes|required|date',
            'medication_id' => 'sometimes|required|exists:medications,id',
            'dosage' => 'sometimes|required|string|max:100',
            'frequency' => 'sometimes|required|string|max:100',
            'duration' => 'sometimes|required|string|max:100',
            'quantity' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string|max:1000',
            'refills' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $prescription->update($request->only([
            'prescription_date',
            'medication_id',
            'dosage',
            'frequency',
            'duration',
            'quantity',
            'instructions',
            'refills',
            'is_active',
            'start_date',
            'end_date',
        ]));

        $prescription->load(['patient', 'consultation', 'prescribedBy', 'medication']);

        return $this->success($prescription, 'Prescription updated successfully');
    }

    /**
     * Remove the specified prescription (admin only)
     */
    public function destroy(Request $request, Prescription $prescription): JsonResponse
    {
        // Only admins can delete prescriptions
        if ($request->user()->role !== 'admin') {
            return $this->error('Access denied. Only administrators can delete prescriptions.', [], 403);
        }

        $prescription->delete();

        return $this->success(null, 'Prescription deleted successfully');
    }

    /**
     * Get prescriptions for a specific patient
     */
    public function getByPatient(Request $request, string $patientId): JsonResponse
    {
        $user = $request->user();
        $query = Prescription::where('patient_id', $patientId)
            ->with(['consultation', 'prescribedBy', 'medication']);

        // Check access
        if ($user->role === 'clinician') {
            $patient = \App\Models\Patient::find($patientId);
            if (!$patient || $patient->created_by !== $user->id) {
                return $this->error('Access denied', [], 403);
            }
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        } else {
            // Default to active only
            $query->where('is_active', true);
        }

        $prescriptions = $query->orderBy('prescription_date', 'desc')->get();

        return $this->success($prescriptions, 'Patient prescriptions retrieved successfully');
    }
}
