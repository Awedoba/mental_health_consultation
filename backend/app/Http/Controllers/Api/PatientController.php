<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\EmergencyContact;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends ApiController
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Patient::with(['emergencyContacts', 'createdBy']);

        // Role-based filtering
        if ($user->role === 'clinician') {
            // Clinicians see only their own patients
            $query->where('created_by', $user->id);
        }
        // Admins see all patients

        // Search filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        } else {
            // Default to active only
            $query->where('is_active', true);
        }

        // Filter by age range (SQLite compatible)
        if ($request->has('age_min')) {
            $minDate = now()->subYears($request->age_min)->format('Y-m-d');
            $query->where('date_of_birth', '<=', $minDate);
        }
        if ($request->has('age_max')) {
            $maxDate = now()->subYears($request->age_max + 1)->addDay()->format('Y-m-d');
            $query->where('date_of_birth', '>=', $maxDate);
        }

        $patients = $query->orderBy('last_name')->orderBy('first_name')->paginate(20);

        return $this->paginated($patients, 'Patients retrieved successfully');
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,non_binary,prefer_not_say,other',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'address_line1' => 'required|string|max:100',
            'address_line2' => 'nullable|string|max:100',
            'city' => 'required|string|max:50',
            'state_province' => 'required|string|max:50',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:50',
            'marital_status' => 'nullable|in:single,married,divorced,widowed,separated',
            'occupation' => 'nullable|string|max:100',
            'education_level' => 'nullable|in:none,primary,secondary,undergraduate,graduate,doctoral',
            'emergency_contacts' => 'required|array|min:1',
            'emergency_contacts.*.contact_name' => 'required|string|max:100',
            'emergency_contacts.*.relationship' => 'required|in:spouse,parent,child,sibling,friend,other',
            'emergency_contacts.*.phone_number' => 'required|string|max:20',
            'emergency_contacts.*.alternate_phone' => 'nullable|string|max:20',
            'emergency_contacts.*.email' => 'nullable|email|max:100',
            'emergency_contacts.*.address' => 'nullable|string|max:200',
            'emergency_contacts.*.is_primary' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        // Check for duplicate (name + DOB)
        $duplicate = Patient::where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->first();

        if ($duplicate) {
            return $this->error('Potential duplicate patient found. Please verify before creating.', [
                'duplicate_id' => $duplicate->id,
            ], 422);
        }

        // Validate age (0-120 years)
        $dob = \Carbon\Carbon::parse($request->date_of_birth);
        $age = $dob->age;
        if ($age < 0 || $age > 120) {
            return $this->error('Invalid date of birth. Age must be between 0 and 120 years.', [], 422);
        }

        $patient = Patient::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'state_province' => $request->state_province,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'marital_status' => $request->marital_status,
            'occupation' => $request->occupation,
            'education_level' => $request->education_level,
            'is_active' => true,
            'created_by' => $request->user()->id,
        ]);

        // Create emergency contacts
        foreach ($request->emergency_contacts as $contactData) {
            EmergencyContact::create([
                'patient_id' => $patient->id,
                'contact_name' => $contactData['contact_name'],
                'relationship' => $contactData['relationship'],
                'phone_number' => $contactData['phone_number'],
                'alternate_phone' => $contactData['alternate_phone'] ?? null,
                'email' => $contactData['email'] ?? null,
                'address' => $contactData['address'] ?? null,
                'is_primary' => $contactData['is_primary'] ?? false,
            ]);
        }

        $patient->load('emergencyContacts', 'createdBy');

        return $this->success($patient, 'Patient created successfully', 201);
    }

    /**
     * Display the specified patient
     */
    public function show(Request $request, Patient $patient): JsonResponse
    {
        $user = $request->user();

        // Check access
        if ($user->role === 'clinician' && $patient->created_by !== $user->id) {
            return $this->error('Access denied', [], 403);
        }

        $patient->load(['emergencyContacts', 'createdBy', 'consultations.primaryClinician']);

        return $this->success($patient);
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient): JsonResponse
    {
        $user = $request->user();

        // Check access
        if ($user->role === 'clinician' && $patient->created_by !== $user->id) {
            return $this->error('Access denied', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'sometimes|required|string|max:50',
            'date_of_birth' => 'sometimes|required|date|before:today',
            'gender' => 'sometimes|required|in:male,female,non_binary,prefer_not_say,other',
            'phone_number' => 'sometimes|required|string|max:20',
            'email' => 'nullable|email|max:100',
            'address_line1' => 'sometimes|required|string|max:100',
            'address_line2' => 'nullable|string|max:100',
            'city' => 'sometimes|required|string|max:50',
            'state_province' => 'sometimes|required|string|max:50',
            'postal_code' => 'sometimes|required|string|max:20',
            'country' => 'sometimes|required|string|max:50',
            'marital_status' => 'nullable|in:single,married,divorced,widowed,separated',
            'occupation' => 'nullable|string|max:100',
            'education_level' => 'nullable|in:none,primary,secondary,undergraduate,graduate,doctoral',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        // Check for duplicate if name or DOB changed
        if ($request->has('first_name') || $request->has('last_name') || $request->has('date_of_birth')) {
            $duplicate = Patient::where('first_name', $request->first_name ?? $patient->first_name)
                ->where('last_name', $request->last_name ?? $patient->last_name)
                ->where('date_of_birth', $request->date_of_birth ?? $patient->date_of_birth)
                ->where('id', '!=', $patient->id)
                ->first();

            if ($duplicate) {
                return $this->error('Potential duplicate patient found.', [
                    'duplicate_id' => $duplicate->id,
                ], 422);
            }
        }

        $patient->update($request->only([
            'first_name',
            'middle_name',
            'last_name',
            'date_of_birth',
            'gender',
            'phone_number',
            'email',
            'address_line1',
            'address_line2',
            'city',
            'state_province',
            'postal_code',
            'country',
            'marital_status',
            'occupation',
            'education_level',
        ]));

        // Handle emergency contacts
        if ($request->has('emergency_contacts')) {
            $currentIds = collect($request->emergency_contacts)->pluck('id')->filter()->toArray();
            
            // Delete contacts that are no longer in the list
            $patient->emergencyContacts()->whereNotIn('id', $currentIds)->delete();

            foreach ($request->emergency_contacts as $contactData) {
                if (isset($contactData['id'])) {
                    // Update existing contact
                    $contact = $patient->emergencyContacts()->find($contactData['id']);
                    if ($contact) {
                        $contact->update([
                            'contact_name' => $contactData['contact_name'],
                            'relationship' => $contactData['relationship'],
                            'phone_number' => $contactData['phone_number'],
                            'alternate_phone' => $contactData['alternate_phone'] ?? null,
                            'email' => $contactData['email'] ?? null,
                            'address' => $contactData['address'] ?? null,
                            'is_primary' => $contactData['is_primary'] ?? false,
                        ]);
                    }
                } else {
                    // Create new contact
                    EmergencyContact::create([
                        'patient_id' => $patient->id,
                        'contact_name' => $contactData['contact_name'],
                        'relationship' => $contactData['relationship'],
                        'phone_number' => $contactData['phone_number'],
                        'alternate_phone' => $contactData['alternate_phone'] ?? null,
                        'email' => $contactData['email'] ?? null,
                        'address' => $contactData['address'] ?? null,
                        'is_primary' => $contactData['is_primary'] ?? false,
                    ]);
                }
            }
        }

        $patient->load('emergencyContacts', 'createdBy');

        return $this->success($patient, 'Patient updated successfully');
    }

    /**
     * Remove the specified patient (soft delete - admin only)
     */
    public function destroy(Request $request, Patient $patient): JsonResponse
    {
        // Only admins can delete
        if ($request->user()->role !== 'admin') {
            return $this->error('Access denied. Only administrators can delete patients.', [], 403);
        }

        $patient->is_active = false;
        $patient->save();

        return $this->success(null, 'Patient archived successfully');
    }
}
