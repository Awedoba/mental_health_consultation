<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Create New Prescription</h1>
      <NuxtLink
        to="/prescriptions"
        class="text-gray-600 hover:text-gray-900"
      >
        Cancel
      </NuxtLink>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <FormError :error="error" :errors="validationErrors" />

      <!-- Patient Selection -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Patient Information</h2>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Select Patient *</label>
          <div v-if="loadingPatients" class="text-sm text-gray-500">Loading patients...</div>
          <select
            v-else
            v-model="form.patient_id"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            @change="onPatientChange"
          >
            <option value="">Select a patient</option>
            <option v-for="patient in patients" :key="patient.id" :value="patient.id">
              {{ patient.last_name }}, {{ patient.first_name }} (DOB: {{ formatDate(patient.date_of_birth) }})
            </option>
          </select>
        </div>

        <!-- Consultation Selection (Optional) -->
        <div v-if="form.patient_id" class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Link to Consultation (Optional)</label>
          <div v-if="loadingConsultations" class="text-sm text-gray-500">Loading consultations...</div>
          <select
            v-else
            v-model="form.consultation_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">None</option>
            <option v-for="consultation in consultations" :key="consultation.id" :value="consultation.id">
              {{ formatDate(consultation.consultation_date) }} - {{ formatSessionType(consultation.session_type) }}
            </option>
          </select>
        </div>
      </div>

      <!-- Medication Selection -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Medication</h2>
        <MedicationPicker
          v-model="form.medication_id"
          label="Select Medication"
          required
          @medication-selected="onMedicationSelected"
        />
      </div>

      <!-- Prescription Details -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Prescription Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prescription Date *</label>
            <input
              v-model="form.prescription_date"
              type="date"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dosage *</label>
            <input
              v-model="form.dosage"
              type="text"
              required
              placeholder="e.g., 10mg twice daily"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Frequency *</label>
            <select
              v-model="form.frequency"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="">Select frequency</option>
              <option value="Once daily (OD)">Once daily (OD)</option>
              <option value="Twice daily (BID)">Twice daily (BID)</option>
              <option value="Three times daily (TID)">Three times daily (TID)</option>
              <option value="Four times daily (QID)">Four times daily (QID)</option>
              <option value="As needed (PRN)">As needed (PRN)</option>
              <option value="Every 6 hours">Every 6 hours</option>
              <option value="Every 8 hours">Every 8 hours</option>
              <option value="Every 12 hours">Every 12 hours</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Duration *</label>
            <input
              v-model="form.duration"
              type="text"
              required
              placeholder="e.g., 7 days, 1 month"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
            <input
              v-model.number="form.quantity"
              type="number"
              min="1"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Refills</label>
            <input
              v-model.number="form.refills"
              type="number"
              min="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input
              v-model="form.start_date"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input
              v-model="form.end_date"
              type="date"
              :min="form.start_date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>
        </div>

        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Special Instructions</label>
          <textarea
            v-model="form.instructions"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Any special instructions for taking this medication..."
          ></textarea>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-4">
        <NuxtLink
          to="/prescriptions"
          class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </NuxtLink>
        <button
          type="submit"
          :disabled="saving"
          class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
        >
          {{ saving ? 'Creating...' : 'Create Prescription' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import type { Patient, Consultation, Medication } from '~/types'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const router = useRouter()
const { create } = usePrescriptions()
const { list: listPatients } = usePatients()
const { list: listConsultations } = useConsultations()
const { showToast } = useToast()
const { handleApiError } = useErrorHandler()

const patients = ref<Patient[]>([])
const consultations = ref<Consultation[]>([])
const loadingPatients = ref(true)
const loadingConsultations = ref(false)
const saving = ref(false)
const error = ref('')
const validationErrors = ref<Record<string, string[]> | null>(null)

const form = ref({
  patient_id: '',
  consultation_id: '',
  prescription_date: new Date().toISOString().split('T')[0],
  medication_id: '',
  dosage: '',
  frequency: '',
  duration: '',
  quantity: undefined as number | undefined,
  instructions: '',
  refills: 0,
  start_date: '',
  end_date: '',
})

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

const formatSessionType = (type: string) => {
  return type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const onMedicationSelected = (medication: Medication) => {
  // Auto-fill dosage if medication has strength
  if (medication.strength && !form.value.dosage) {
    form.value.dosage = medication.strength
  }
}

const onPatientChange = async () => {
  if (form.value.patient_id) {
    loadingConsultations.value = true
    const result = await listConsultations({ patient_id: form.value.patient_id })
    if (result.success) {
      consultations.value = result.data
    }
    loadingConsultations.value = false
  } else {
    consultations.value = []
    form.value.consultation_id = ''
  }
}

const handleSubmit = async () => {
  saving.value = true
  error.value = ''
  validationErrors.value = null

  const prescriptionData = {
    ...form.value,
    consultation_id: form.value.consultation_id || undefined,
    quantity: form.value.quantity || undefined,
    start_date: form.value.start_date || undefined,
    end_date: form.value.end_date || undefined,
  }

  const result = await create(prescriptionData)

  if (result.success && result.data) {
    showToast('success', 'Prescription created successfully')
    router.push(`/prescriptions/${result.data.id}`)
  } else {
    error.value = result.error || 'Failed to create prescription'
    validationErrors.value = result.errors || null
    handleApiError(result.error || 'Failed to create prescription')
  }

  saving.value = false
}

// Load patients
onMounted(async () => {
  loadingPatients.value = true
  const result = await listPatients({ is_active: true })
  if (result.success) {
    patients.value = result.data
  }
  loadingPatients.value = false
})
</script>

