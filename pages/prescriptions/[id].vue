<template>
  <div>
    <div class="mb-6 flex justify-between items-center">
      <NuxtLink
        to="/prescriptions"
        class="text-indigo-600 hover:text-indigo-900 flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Prescriptions
      </NuxtLink>
    </div>

    <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading prescription...</LoadingSpinner>

    <FormError :error="error" />

    <div v-if="!loading && prescription" class="space-y-6">
      <!-- Header -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Prescription Details</h1>
            <p class="mt-2 text-sm text-gray-500">
              Prescribed on {{ formatDate(prescription.prescription_date) }}
            </p>
          </div>
          <div class="flex gap-2">
            <span
              :class="[
                'inline-flex px-3 py-1 text-sm font-semibold rounded-full',
                isActive(prescription) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
              ]"
            >
              {{ isActive(prescription) ? 'Active' : 'Completed/Expired' }}
            </span>
             <!-- Add Edit button later if needed, mimicking consultation structure -->
          </div>
        </div>
      </div>

      <!-- Patient Information -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-start mb-4">
          <h2 class="text-xl font-semibold text-gray-900">Patient Information</h2>
          <NuxtLink
            v-if="prescription.patient_id"
            :to="`/patients/${prescription.patient_id}`"
            class="text-indigo-600 hover:text-indigo-900 text-sm"
          >
            View Patient Profile →
          </NuxtLink>
        </div>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
          <div>
            <dt class="text-sm font-medium text-gray-500">Patient Name</dt>
            <dd class="mt-1 text-sm text-gray-900 font-medium">
              {{ prescription.patient ? `${prescription.patient.first_name} ${prescription.patient.last_name}` : 'N/A' }}
            </dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Prescribing Clinician</dt>
            <dd class="mt-1 text-sm text-gray-900">
               <!-- Assuming clinician is also a relation, potentially on the consultation or direct -->
               <!-- index.vue doesn't show clinician, but consultation details did. -->
               <!-- checking backend types or response would confirm, but sticking to safe fallback -->
              {{ prescription.clinician?.first_name ? `${prescription.clinician.first_name} ${prescription.clinician.last_name}` : (prescription.clinician_name || 'N/A') }}
            </dd>
          </div>
        </dl>
      </div>

      <!-- Medication Details -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Medication Details</h2>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 md:grid-cols-3">
          <div class="col-span-1 md:col-span-3">
            <dt class="text-sm font-medium text-gray-500">Medication Name</dt>
            <dd class="mt-1 text-lg text-gray-900 font-bold">{{ prescription.medication?.name || prescription.medication_name || 'N/A' }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Dosage</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ prescription.dosage }}</dd>
          </div>
          <div>
             <dt class="text-sm font-medium text-gray-500">Frequency</dt>
             <dd class="mt-1 text-sm text-gray-900">{{ prescription.frequency }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Duration</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ prescription.duration }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Quantity</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ prescription.quantity || 'N/A' }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Refills</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ prescription.refills }}</dd>
          </div>
        </dl>
      </div>

      <!-- Instructions -->
      <div v-if="prescription.instructions" class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Instructions</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ prescription.instructions }}</p>
      </div>
      
       <!-- Timeline / Validity -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Validity Period</h2>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
          <div>
            <dt class="text-sm font-medium text-gray-500">Start Date</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(prescription.start_date) }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">End Date</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(prescription.end_date) }}</dd>
          </div>
        </dl>
      </div>

      <!-- Linked Consultation -->
       <div v-if="prescription.consultation_id" class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Related Consultation</h2>
             <NuxtLink
                :to="`/consultations/${prescription.consultation_id}`"
                class="text-indigo-600 hover:text-indigo-900 text-sm"
              >
                View Consultation →
              </NuxtLink>
        </div>
      </div>

      <!-- System Info -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Record Information</h2>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
          <div>
            <dt class="text-sm font-medium text-gray-500">Created At</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(prescription.created_at) }}</dd>
          </div>
          <div>
             <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
             <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(prescription.updated_at) }}</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Prescription } from '~/types'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const route = useRoute()
const { show } = usePrescriptions()
const { handleApiError } = useErrorHandler()

const prescription = ref<Prescription | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

const loadPrescription = async () => {
  loading.value = true
  error.value = null

  const result = await show(route.params.id as string)

  if (result.success && result.data) {
    prescription.value = result.data
  } else {
    error.value = result.error || 'Failed to load prescription'
    handleApiError({ data: { error: { message: result.error } } }, 'Failed to load prescription')
  }

  loading.value = false
}

const formatDate = (dateString?: string) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatDateTime = (dateString?: string) => {
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

const isActive = (p: Prescription) => {
    if (!p.end_date) return true // Assume active if no end date
    const end = new Date(p.end_date)
    const now = new Date()
    return end >= now
}

onMounted(() => {
  loadPrescription()
})
</script>
