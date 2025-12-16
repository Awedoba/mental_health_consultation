<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Edit Consultation</h1>
      <NuxtLink
        :to="`/consultations/${route.params.id}`"
        class="text-gray-600 hover:text-gray-900"
      >
        Cancel
      </NuxtLink>
    </div>

    <div v-if="loading" class="text-center py-8">
      <LoadingSpinner size="lg" text="Loading consultation..." />
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
      {{ error }}
    </div>

    <div v-else-if="isLocked" class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-4">
      <p class="font-bold">Consultation Locked</p>
      <p>This consultation has been finalized and cannot be edited.</p>
      <div class="mt-4">
        <NuxtLink
          :to="`/consultations/${route.params.id}`"
          class="text-indigo-600 hover:text-indigo-800 font-medium"
        >
          Return to details
        </NuxtLink>
      </div>
    </div>

    <form v-else @submit.prevent="handleSubmit" class="space-y-6">
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
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">History of Present Illness *</label>
            <textarea
              v-model="form.history_present_illness"
              required
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            ></textarea>
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
          </div>
        </div>
      </div>

      <!-- Assessment & Plan -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Assessment & Plan</h2>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis</label>
            <textarea
              v-model="form.diagnosis"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Treatment Plan</label>
            <textarea
              v-model="form.treatment_plan"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-4">
        <NuxtLink
          :to="`/consultations/${route.params.id}`"
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
          {{ saving ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>

      <FormError :error="saveError" />
    </form>
  </div>
</template>

<script setup lang="ts">
import type { Consultation } from '~/types'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const route = useRoute()
const router = useRouter()
const { show, update } = useConsultations()
const { showToast } = useToast()

const loading = ref(true)
const saving = ref(false)
const error = ref('')
const saveError = ref('')
const isLocked = ref(false)

const form = ref<Partial<Consultation>>({
  consultation_date: '',
  consultation_time: '',
  session_duration: 60,
  session_type: 'follow_up',
  chief_complaint: '',
  history_present_illness: '',
  clinical_summary: '',
  diagnosis: '',
  treatment_plan: '',
  risk_assessment: 'low',
})

// Load consultation data
onMounted(async () => {
  loading.value = true
  const result = await show(route.params.id as string)

  if (result.success && result.data) {
    const data = result.data
    
    if (data.is_locked) {
      isLocked.value = true
    }

    // Format date for input (YYYY-MM-DD)
    const formatDate = (dateString: string | null | undefined): string => {
      if (!dateString) return ''
      // If already in YYYY-MM-DD format, return as is
      if (typeof dateString === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        return dateString
      }
      // Otherwise parse and format
      const date = new Date(dateString)
      if (isNaN(date.getTime())) return ''
      return date.toISOString().split('T')[0]
    }

    // Format time for input (HH:MM)
    const formatTime = (timeString: string | null | undefined): string => {
      if (!timeString) return ''
      // If already in HH:MM format, return as is
      if (typeof timeString === 'string' && /^\d{2}:\d{2}$/.test(timeString)) {
        return timeString
      }
      // Otherwise parse and format
      const date = new Date(`2000-01-01T${timeString}`)
      if (isNaN(date.getTime())) return ''
      return date.toTimeString().slice(0, 5)
    }

    // Populate form with all available fields
    form.value = {
      consultation_date: formatDate(data.consultation_date),
      consultation_time: formatTime(data.consultation_time),
      session_duration: data.session_duration || null,
      session_type: data.session_type || '',
      chief_complaint: data.chief_complaint || '',
      history_present_illness: data.history_present_illness || '',
      clinical_summary: data.clinical_summary || '',
      risk_assessment: data.risk_assessment || 'low',
      // These may come from relationships
      diagnosis: data.diagnosis || '',
      treatment_plan: data.treatment_plan || '',
    }
  } else {
    error.value = result.error || 'Failed to load consultation'
  }

  loading.value = false
})

// Handle submission
const handleSubmit = async () => {
  saving.value = true
  saveError.value = ''

  const result = await update(route.params.id as string, form.value)

  if (result.success) {
    showToast('success', 'Consultation updated successfully')
    router.push(`/consultations/${route.params.id}`)
  } else {
    saveError.value = result.error || 'Failed to update consultation'
  }

  saving.value = false
}
</script>
