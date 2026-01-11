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
            <button
              v-if="!consultation.is_locked"
              @click="showLockModal = true"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
              Complete
            </button>
          </div>
        </div>
      </div>

      <!-- Lock Confirmation Modal -->
      <div v-if="showLockModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showLockModal = false"></div>
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
          <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Complete Consultation</h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to complete this consultation? This action will lock the record and prevent any further edits.
                  </p>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                :disabled="locking"
                @click="lockConsultation"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                {{ locking ? 'Completing...' : 'Yes, Complete' }}
              </button>
              <button
                type="button"
                :disabled="locking"
                @click="showLockModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
              >
                Cancel
              </button>
            </div>
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
const { show, lock } = useConsultations()
const { handleApiError } = useErrorHandler()

const consultation = ref<Consultation | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const showLockModal = ref(false)
const locking = ref(false)

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

const lockConsultation = async () => {
  if (!consultation.value) return
  
  locking.value = true
  
  const result = await lock(consultation.value.id)
  
  if (result.success && result.data) {
    consultation.value = result.data
    showLockModal.value = false
  } else {
    handleApiError({ data: { error: { message: result.error } } }, 'Failed to complete consultation')
  }
  
  locking.value = false
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
