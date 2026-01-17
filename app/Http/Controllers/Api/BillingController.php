<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Billing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillingController extends ApiController
{
    /**
     * Display a listing of billings
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Billing::with(['patient', 'consultation', 'createdBy']);

        // Role-based filtering
        if ($user->role === 'clinician') {
            // Clinicians see billings for their patients/consultations
            $query->where(function ($q) use ($user) {
                $q->whereHas('patient', function ($patientQuery) use ($user) {
                    $patientQuery->where('created_by', $user->id);
                })->orWhereHas('consultation', function ($consultationQuery) use ($user) {
                    $consultationQuery->where('primary_clinician_id', $user->id);
                });
            });
        }
        // Admins see all billings

        // Filter by patient
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by consultation
        if ($request->has('consultation_id')) {
            $query->where('consultation_id', $request->consultation_id);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by service type
        if ($request->has('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('billing_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('billing_date', '<=', $request->date_to);
        }

        // Search by invoice number or patient name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $billings = $query->orderBy('billing_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->paginated($billings, 'Billings retrieved successfully');
    }

    /**
     * Store a newly created billing
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'billing_date' => 'required|date',
            'service_type' => 'required|in:consultation,home_visit,medication,other',
            'amount' => 'required|numeric|min:0',
            'nhis_covered' => 'nullable|boolean',
            'nhis_amount' => 'nullable|numeric|min:0',
            'patient_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:pending,partial,paid,waived',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|in:cash,mobile_money,bank_transfer,nhis',
            'invoice_number' => 'nullable|string|max:50|unique:billings,invoice_number',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        // Validate amounts
        if ($request->nhis_covered && $request->nhis_amount) {
            if ($request->nhis_amount > $request->amount) {
                return $this->error('NHIS amount cannot exceed total amount', [], 422);
            }
        }

        if ($request->patient_amount) {
            $totalCovered = ($request->nhis_amount ?? 0) + $request->patient_amount;
            if ($totalCovered > $request->amount) {
                return $this->error('Total covered amount (NHIS + Patient) cannot exceed total amount', [], 422);
            }
        }

        $billing = Billing::create([
            'patient_id' => $request->patient_id,
            'consultation_id' => $request->consultation_id,
            'billing_date' => $request->billing_date,
            'service_type' => $request->service_type,
            'amount' => $request->amount,
            'nhis_covered' => $request->nhis_covered ?? false,
            'nhis_amount' => $request->nhis_amount,
            'patient_amount' => $request->patient_amount,
            'payment_status' => $request->payment_status ?? 'pending',
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'invoice_number' => $request->invoice_number, // Will be auto-generated if not provided
            'notes' => $request->notes,
            'created_by' => $request->user()->id,
        ]);

        $billing->load(['patient', 'consultation', 'createdBy']);

        return $this->success($billing, 'Billing created successfully', 201);
    }

    /**
     * Display the specified billing
     */
    public function show(Request $request, Billing $billing): JsonResponse
    {
        $user = $request->user();

        // Check access
        if ($user->role === 'clinician') {
            $hasAccess = $billing->patient->created_by === $user->id
                || ($billing->consultation && $billing->consultation->primary_clinician_id === $user->id);

            if (! $hasAccess) {
                return $this->error('Access denied', [], 403);
            }
        }

        $billing->load(['patient', 'consultation', 'createdBy']);

        return $this->success($billing);
    }

    /**
     * Update the specified billing
     */
    public function update(Request $request, Billing $billing): JsonResponse
    {
        $user = $request->user();

        // Check access
        if ($user->role === 'clinician') {
            $hasAccess = $billing->patient->created_by === $user->id
                || ($billing->consultation && $billing->consultation->primary_clinician_id === $user->id);

            if (! $hasAccess) {
                return $this->error('Access denied', [], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'billing_date' => 'sometimes|required|date',
            'service_type' => 'sometimes|required|in:consultation,home_visit,medication,other',
            'amount' => 'sometimes|required|numeric|min:0',
            'nhis_covered' => 'nullable|boolean',
            'nhis_amount' => 'nullable|numeric|min:0',
            'patient_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'sometimes|required|in:pending,partial,paid,waived',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|in:cash,mobile_money,bank_transfer,nhis',
            'invoice_number' => 'nullable|string|max:50|unique:billings,invoice_number,' . $billing->id,
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        // Validate amounts
        $amount = $request->amount ?? $billing->amount;
        if ($request->has('nhis_amount') && $request->nhis_amount) {
            if ($request->nhis_amount > $amount) {
                return $this->error('NHIS amount cannot exceed total amount', [], 422);
            }
        }

        if ($request->has('patient_amount') && $request->patient_amount) {
            $nhisAmount = $request->nhis_amount ?? $billing->nhis_amount ?? 0;
            $totalCovered = $nhisAmount + $request->patient_amount;
            if ($totalCovered > $amount) {
                return $this->error('Total covered amount (NHIS + Patient) cannot exceed total amount', [], 422);
            }
        }

        $billing->update($request->only([
            'billing_date',
            'service_type',
            'amount',
            'nhis_covered',
            'nhis_amount',
            'patient_amount',
            'payment_status',
            'payment_date',
            'payment_method',
            'invoice_number',
            'notes',
        ]));

        $billing->load(['patient', 'consultation', 'createdBy']);

        return $this->success($billing, 'Billing updated successfully');
    }

    /**
     * Remove the specified billing (admin only)
     */
    public function destroy(Request $request, Billing $billing): JsonResponse
    {
        // Only admins can delete
        if ($request->user()->role !== 'admin') {
            return $this->error('Access denied. Only administrators can delete billings.', [], 403);
        }

        $billing->delete();

        return $this->success(null, 'Billing deleted successfully');
    }

    /**
     * Get billings for a specific patient
     */
    public function getByPatient(Request $request, string $patientId): JsonResponse
    {
        $user = $request->user();
        $query = Billing::where('patient_id', $patientId)
            ->with(['consultation', 'createdBy']);

        // Check access
        if ($user->role === 'clinician') {
            $patient = \App\Models\Patient::find($patientId);
            if (!$patient || $patient->created_by !== $user->id) {
                return $this->error('Access denied', [], 403);
            }
        }

        $billings = $query->orderBy('billing_date', 'desc')->get();

        return $this->success($billings, 'Patient billings retrieved successfully');
    }
}
