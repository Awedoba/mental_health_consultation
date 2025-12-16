<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">New Consultation</h1>
      <NuxtLink
        to="/consultations"
        class="text-gray-600 hover:text-gray-900"
      >
        Cancel
      </NuxtLink>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
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
          >
            <option value="" disabled>Select a patient</option>
            <option v-for="patient in patients" :key="patient.id" :value="patient.id">
              {{ patient.last_name }}, {{ patient.first_name }} (DOB: {{ formatDate(patient.date_of_birth) }})
            </option>
          </select>
          <div v-if="patientsError" class="text-red-600 text-sm mt-1">{{ patientsError }}</div>
        </div>
      </div>

      <!-- Session Details -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Session Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
            <input
              v-model="form.consultation_date"
              type="date"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Time *</label>
            <input
              v-model="form.consultation_time"
              type="time"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
            <input
              v-model="form.session_duration"
              type="number"
              min="1"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Session Type *</label>
            <select
              v-model="form.session_type"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="initial_assessment">Initial Assessment</option>
              <option value="follow_up">Follow-up</option>
              <option value="crisis_intervention">Crisis Intervention</option>
              <option value="therapy_session">Therapy Session</option>
              <option value="medication_management">Medication Management</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Clinical Notes -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Clinical Notes</h2>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Chief Complaint *</label>
            <textarea
              v-model="form.chief_complaint"
              required
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Primary reason for the visit"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">History of Present Illness *</label>
            <textarea
              v-model="form.history_present_illness"
              required
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Detailed description of the problem"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Risk Assessment *</label>
            <select
              v-model="form.risk_assessment"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              :class="{
                'bg-green-50 text-green-800 border-green-300': form.risk_assessment === 'low',
                'bg-yellow-50 text-yellow-800 border-yellow-300': form.risk_assessment === 'moderate',
                'bg-red-50 text-red-800 border-red-300': form.risk_assessment === 'high'
              }"
            >
              <option value="low">Low Risk</option>
              <option value="moderate">Moderate Risk</option>
              <option value="high">High Risk</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Clinical Summary *</label>
            <textarea
              v-model="form.clinical_summary"
              required
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Summary of clinical findings, assessment, and key observations"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">Provide a concise summary of the clinical assessment</p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-4">
        <NuxtLink
          to="/consultations"
          class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </NuxtLink>
        <button
          type="submit"
          :disabled="saving"
          class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 flex items-center"
        >
          <span v-if="saving" class="mr-2">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          {{ saving ? 'Creating...' : 'Create Consultation' }}
        </button>
      </div>

      <FormError :error="saveError" :errors="validationErrors" />
    </form>
  </div>
</template>

<script setup lang="ts">
import type { Patient } from '~/types'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const router = useRouter()
const { create } = useConsultations()
const { list: listPatients } = usePatients()
const { showToast } = useToast()
const { user } = useAuth()

const patients = ref<Patient[]>([])
const loadingPatients = ref(true)
const patientsError = ref('')
const saving = ref(false)
const saveError = ref('')
const validationErrors = ref<Record<string, string[]> | null>(null)

const form = ref({
  patient_id: '',
  consultation_date: new Date().toISOString().split('T')[0],
  consultation_time: new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' }),
  session_duration: 60,
  session_type: 'follow_up',
  chief_complaint: '',
  history_present_illness: '',
  risk_assessment: 'low' as 'low' | 'moderate' | 'high',
  clinical_summary: '',
})

// Load active patients
onMounted(async () => {
  loadingPatients.value = true
  const result = await listPatients({ is_active: true })

  if (result.success && result.data) {
    patients.value = result.data
  } else {
    patientsError.value = result.error || 'Failed to load patients'
  }
  loadingPatients.value = false
})

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

// Handle submission
const handleSubmit = async () => {
  if (!user.value?.id) {
    saveError.value = 'User not authenticated'
    return
  }

  saving.value = true
  saveError.value = ''
  validationErrors.value = null

  const result = await create({
    ...form.value,
    primary_clinician_id: user.value.id,
  })

  if (result.success && result.data) {
    showToast('success', 'Consultation created successfully')
    router.push(`/consultations/${result.data.id}`)
  } else {
    saveError.value = result.error || 'Failed to create consultation'
    validationErrors.value = result.errors || null
  }

  saving.value = false
}
</script>
