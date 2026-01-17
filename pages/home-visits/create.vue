<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Create New Home Visit</h1>
      <NuxtLink
        to="/home-visits"
        class="text-gray-600 hover:text-gray-900"
      >
        Cancel
      </NuxtLink>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <FormError :error="error" :errors="validationErrors" />

      <!-- Client Information -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Client Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Client Name *</label>
            <input
              v-model="form.client_name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Link to Patient (Optional)</label>
            <div v-if="loadingPatients" class="text-sm text-gray-500">Loading patients...</div>
            <select
              v-else
              v-model="form.patient_id"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="">None (Standalone visit)</option>
              <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                {{ patient.last_name }}, {{ patient.first_name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
            <input
              v-model.number="form.age"
              type="number"
              min="0"
              max="150"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
            <select
              v-model="form.sex"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="">Select...</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Community or Location *</label>
            <input
              v-model="form.community_location"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contact *</label>
            <input
              v-model="form.contact"
              type="tel"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>
        </div>
      </div>

      <!-- Visit Details -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Visit Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Visit Date *</label>
            <input
              v-model="form.visit_date"
              type="date"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Visit Time</label>
            <input
              v-model="form.visit_time"
              type="time"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>
        </div>
      </div>

      <!-- Clinical Information -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Clinical Information</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis or Condition</label>
            <textarea
              v-model="form.diagnosis_condition"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Diagnosis or condition of client"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Medication or Prescription</label>
            <textarea
              v-model="form.medication_prescription"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Medication or prescription provided"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
            <textarea
              v-model="form.observations"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Observations seen during the visit"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Impression</label>
            <textarea
              v-model="form.impression"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Clinical impression"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Management</label>
            <textarea
              v-model="form.management"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Management plan"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Recommendation</label>
            <textarea
              v-model="form.recommendation"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Recommendations for follow-up or observation"
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-4">
        <NuxtLink
          to="/home-visits"
          class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </NuxtLink>
        <button
          type="submit"
          :disabled="saving"
          class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
        >
          {{ saving ? 'Creating...' : 'Create Home Visit' }}
        </button>
      </div>
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
const { create } = useHomeVisits()
const { list: listPatients } = usePatients()
const { showToast } = useToast()
const { handleApiError } = useErrorHandler()

const patients = ref<Patient[]>([])
const loadingPatients = ref(false)
const saving = ref(false)
const error = ref('')
const validationErrors = ref<Record<string, string[]> | null>(null)

const form = ref({
  patient_id: '',
  client_name: '',
  age: undefined as number | undefined,
  sex: '' as 'male' | 'female' | 'other' | '',
  community_location: '',
  contact: '',
  visit_date: new Date().toISOString().split('T')[0],
  visit_time: '',
  diagnosis_condition: '',
  medication_prescription: '',
  observations: '',
  impression: '',
  management: '',
  recommendation: '',
})

const handleSubmit = async () => {
  saving.value = true
  error.value = ''
  validationErrors.value = null

  const homeVisitData = {
    ...form.value,
    patient_id: form.value.patient_id || undefined,
    age: form.value.age || undefined,
    sex: form.value.sex || undefined,
    visit_time: form.value.visit_time || undefined,
  }

  const result = await create(homeVisitData)

  if (result.success && result.data) {
    showToast('success', 'Home visit created successfully')
    router.push(`/home-visits/${result.data.id}`)
  } else {
    error.value = result.error || 'Failed to create home visit'
    validationErrors.value = result.errors || null
    handleApiError(result.error || 'Failed to create home visit')
  }

  saving.value = false
}

// Load patients (optional)
onMounted(async () => {
  loadingPatients.value = true
  const result = await listPatients({ is_active: true })
  if (result.success) {
    patients.value = result.data
  }
  loadingPatients.value = false
})
</script>

