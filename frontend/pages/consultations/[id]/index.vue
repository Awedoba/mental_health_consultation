<template>
  <div>
    <div class="mb-6 flex justify-between items-center">
      <NuxtLink
        to="/consultations"
        class="text-indigo-600 hover:text-indigo-900 flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Consultations
      </NuxtLink>
    </div>

    <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading consultation...</LoadingSpinner>

    <FormError :error="error" />

    <div v-if="!loading && consultation" class="space-y-6">
      <!-- Header -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Consultation Details</h1>
            <p class="mt-2 text-sm text-gray-500">
              {{ formatDate(consultation.consultation_date) }} at {{ consultation.consultation_time }}
            </p>
          </div>
          <div class="flex gap-2">
            <span
              :class="[
                'inline-flex px-3 py-1 text-sm font-semibold rounded-full',
                consultation.is_locked ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
              ]"
            >
              {{ consultation.is_locked ? 'Completed' : 'In Progress' }}
            </span>
            <NuxtLink
              v-if="!consultation.is_locked"
              :to="`/consultations/${consultation.id}/edit`"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
              </svg>
              Edit
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Patient Information -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-start mb-4">
          <h2 class="text-xl font-semibold text-gray-900">Patient Information</h2>
          <NuxtLink
            v-if="consultation.patient_id"
            :to="`/patients/${consultation.patient_id}`"
            class="text-indigo-600 hover:text-indigo-900 text-sm"
          >
            View Patient Profile →
          </NuxtLink>
        </div>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
          <div>
            <dt class="text-sm font-medium text-gray-500">Patient Name</dt>
            <dd class="mt-1 text-sm text-gray-900 font-medium">
              {{ consultation.patient_name || 'N/A' }}
            </dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Primary Clinician</dt>
            <dd class="mt-1 text-sm text-gray-900">
              {{ consultation.clinician_name || 'N/A' }}
            </dd>
          </div>
        </dl>
      </div>

      <!-- Session Details -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Session Details</h2>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-3">
          <div>
            <dt class="text-sm font-medium text-gray-500">Session Type</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ consultation.session_type }}</dd>
          </div>
          <div v-if="consultation.session_duration">
            <dt class="text-sm font-medium text-gray-500">Duration</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ consultation.session_duration }} minutes</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Risk Assessment</dt>
            <dd class="mt-1">
              <span
                :class="[
                  'inline-flex px-2 text-xs font-semibold leading-5 rounded-full',
                  getRiskColor(consultation.risk_assessment)
                ]"
              >
                {{ consultation.risk_assessment }}
              </span>
            </dd>
          </div>
        </dl>
      </div>

      <!-- Chief Complaint -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Chief Complaint</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ consultation.chief_complaint }}</p>
      </div>

      <!-- History of Present Illness -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">History of Present Illness</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ consultation.history_present_illness }}</p>
      </div>

      <!-- Clinical Summary (if available) -->
      <div v-if="consultation.clinical_summary" class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Clinical Summary</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ consultation.clinical_summary }}</p>
      </div>

      <!-- Mental Status Examination (if available) -->
      <div v-if="consultation.mental_status_exam" class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Mental Status Examination</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ consultation.mental_status_exam }}</p>
      </div>

      <!-- Diagnosis (if available) -->
      <div v-if="consultation.diagnosis" class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Diagnosis</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ consultation.diagnosis }}</p>
      </div>

      <!-- Treatment Plan (if available) -->
      <div v-if="consultation.treatment_plan" class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Treatment Plan</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ consultation.treatment_plan }}</p>
      </div>

      <!-- Timestamps -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Record Information</h2>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
          <div>
            <dt class="text-sm font-medium text-gray-500">Created</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(consultation.created_at) }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(consultation.updated_at) }}</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Consultation } from '~/types'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const route = useRoute()
const { show } = useConsultations()
const { handleApiError } = useErrorHandler()

const consultation = ref<Consultation | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

const loadConsultation = async () => {
  loading.value = true
  error.value = null

  const result = await show(route.params.id as string)

  if (result.success && result.data) {
    consultation.value = result.data
  } else {
    error.value = result.error || 'Failed to load consultation'
    handleApiError({ data: { error: { message: result.error } } }, 'Failed to load consultation')
  }

  loading.value = false
}

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatDateTime = (dateString: string) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getRiskColor = (risk: string) => {
  switch (risk) {
    case 'low':
      return 'bg-green-100 text-green-800'
    case 'moderate':
      return 'bg-yellow-100 text-yellow-800'
    case 'high':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

onMounted(() => {
  loadConsultation()
})
</script>
