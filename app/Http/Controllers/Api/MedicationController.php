<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Medication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicationController extends ApiController
{
    /**
     * Display a listing of medications (searchable)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Medication::query();

        // Only show active medications by default
        if (!$request->has('include_inactive')) {
            $query->where('is_active', true);
        }

        // Search by name or generic name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by dosage form
        if ($request->has('dosage_form')) {
            $query->where('dosage_form', $request->dosage_form);
        }

        $medications = $query->orderBy('name')->paginate($request->get('per_page', 50));

        return $this->paginated($medications, 'Medications retrieved successfully');
    }

    /**
     * Store a newly created medication (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        // Only admins can create medications
        if ($request->user()->role !== 'admin') {
            return $this->error('Access denied. Only administrators can create medications.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200|unique:medications,name',
            'generic_name' => 'nullable|string|max:200',
            'dosage_form' => 'nullable|in:tablet,capsule,syrup,injection,other',
            'strength' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $medication = Medication::create([
            'name' => $request->name,
            'generic_name' => $request->generic_name,
            'dosage_form' => $request->dosage_form,
            'strength' => $request->strength,
            'category' => $request->category,
            'is_active' => $request->is_active ?? true,
        ]);

        return $this->success($medication, 'Medication created successfully', 201);
    }

    /**
     * Display the specified medication
     */
    public function show(Medication $medication): JsonResponse
    {
        return $this->success($medication);
    }

    /**
     * Update the specified medication (admin only)
     */
    public function update(Request $request, Medication $medication): JsonResponse
    {
        // Only admins can update medications
        if ($request->user()->role !== 'admin') {
            return $this->error('Access denied. Only administrators can update medications.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:200|unique:medications,name,' . $medication->id,
            'generic_name' => 'nullable|string|max:200',
            'dosage_form' => 'nullable|in:tablet,capsule,syrup,injection,other',
            'strength' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $medication->update($request->only([
            'name',
            'generic_name',
            'dosage_form',
            'strength',
            'category',
            'is_active',
        ]));

        return $this->success($medication, 'Medication updated successfully');
    }

    /**
     * Remove the specified medication (admin only)
     */
    public function destroy(Request $request, Medication $medication): JsonResponse
    {
        // Only admins can delete medications
        if ($request->user()->role !== 'admin') {
            return $this->error('Access denied. Only administrators can delete medications.', [], 403);
        }

        // Check if medication is used in prescriptions
        if ($medication->prescriptions()->exists()) {
            // Soft delete by deactivating
            $medication->is_active = false;
            $medication->save();
            return $this->success(null, 'Medication deactivated (has active prescriptions)');
        }

        $medication->delete();

        return $this->success(null, 'Medication deleted successfully');
    }
}
