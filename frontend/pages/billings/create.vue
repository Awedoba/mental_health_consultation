<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Create New Billing</h1>
      <NuxtLink
        to="/billings"
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
              {{ formatDate(consultation.consultation_date) }} - {{ consultation.session_type }}
            </option>
          </select>
        </div>
      </div>

      <!-- Billing Details -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Billing Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Billing Date *</label>
            <input
              v-model="form.billing_date"
              type="date"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Service Type *</label>
            <select
              v-model="form.service_type"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="consultation">Consultation</option>
              <option value="home_visit">Home Visit</option>
              <option value="medication">Medication</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
            <input
              v-model.number="form.amount"
              type="number"
              step="0.01"
              min="0"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status *</label>
            <select
              v-model="form.payment_status"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="pending">Pending</option>
              <option value="partial">Partial</option>
              <option value="paid">Paid</option>
              <option value="waived">Waived</option>
            </select>
          </div>
        </div>
      </div>

      <!-- NHIS Information -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">NHIS Information</h2>
        <div class="space-y-4">
          <div class="flex items-center">
            <input
              v-model="form.nhis_covered"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
            <label class="ml-2 block text-sm text-gray-900">
              NHIS Covered
            </label>
          </div>

          <div v-if="form.nhis_covered" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">NHIS Amount</label>
              <input
                v-model.number="form.nhis_amount"
                type="number"
                step="0.01"
                min="0"
                :max="form.amount"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Patient Amount</label>
              <input
                v-model.number="form.patient_amount"
                type="number"
                step="0.01"
                min="0"
                :max="form.amount"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Information -->
      <div v-if="form.payment_status === 'paid' || form.payment_status === 'partial'" class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Payment Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
            <input
              v-model="form.payment_date"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
            <select
              v-model="form.payment_method"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="">Select method</option>
              <option value="cash">Cash</option>
              <option value="mobile_money">Mobile Money</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="nhis">NHIS</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Notes -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Additional Notes</h2>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
          <textarea
            v-model="form.notes"
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Any additional notes about this billing..."
          ></textarea>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-4">
        <NuxtLink
          to="/billings"
          class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </NuxtLink>
        <button
          type="submit"
          :disabled="saving"
          class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
        >
          {{ saving ? 'Creating...' : 'Create Billing' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import type { Patient, Consultation } from '~/types'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const router = useRouter()
const { create } = useBillings()
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
  billing_date: new Date().toISOString().split('T')[0],
  service_type: 'consultation' as 'consultation' | 'home_visit' | 'medication' | 'other',
  amount: 0,
  nhis_covered: false,
  nhis_amount: undefined as number | undefined,
  patient_amount: undefined as number | undefined,
  payment_status: 'pending' as 'pending' | 'partial' | 'paid' | 'waived',
  payment_date: '',
  payment_method: '' as 'cash' | 'mobile_money' | 'bank_transfer' | 'nhis' | '',
  notes: '',
})

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
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

  const billingData = {
    ...form.value,
    consultation_id: form.value.consultation_id || undefined,
    nhis_amount: form.value.nhis_amount || undefined,
    patient_amount: form.value.patient_amount || undefined,
    payment_date: form.value.payment_date || undefined,
    payment_method: form.value.payment_method || undefined,
  }

  const result = await create(billingData)

  if (result.success && result.data) {
    showToast('success', 'Billing created successfully')
    router.push(`/billings/${result.data.id}`)
  } else {
    error.value = result.error || 'Failed to create billing'
    validationErrors.value = result.errors || null
    handleApiError(result.error || 'Failed to create billing')
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

