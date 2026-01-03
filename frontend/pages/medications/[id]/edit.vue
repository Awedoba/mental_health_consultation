<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Edit Medication</h1>
      <NuxtLink
        to="/medications"
        class="text-gray-600 hover:text-gray-900"
      >
        Cancel
      </NuxtLink>
    </div>

    <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading medication...</LoadingSpinner>

    <form v-else @submit.prevent="handleSubmit" class="max-w-3xl mx-auto space-y-6">
      <FormError :error="error" :errors="validationErrors" />

      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Medication Details</h2>
        
        <div class="grid grid-cols-1 gap-6">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="e.g. Paracetamol"
            />
          </div>

          <!-- Generic Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Generic Name</label>
            <input
              v-model="form.generic_name"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="e.g. Acetaminophen"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Strength -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Strength</label>
              <input
                v-model="form.strength"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="e.g. 500mg"
              />
            </div>

            <!-- Dosage Form -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Dosage Form</label>
              <select
                v-model="form.dosage_form"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option value="">Select form</option>
                <option value="Tablet">Tablet</option>
                <option value="Capsule">Capsule</option>
                <option value="Liquid">Liquid</option>
                <option value="Injection">Injection</option>
                <option value="Cream">Cream</option>
                <option value="Ointment">Ointment</option>
                <option value="Inhaler">Inhaler</option>
                <option value="Drops">Drops</option>
                <option value="Suppository">Suppository</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>

          <!-- Category -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <input
              v-model="form.category"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="e.g. Analgesic"
            />
          </div>

          <!-- Is Active -->
          <div class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              id="is_active"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
            <label for="is_active" class="ml-2 block text-sm text-gray-900">
              Active (available for prescription)
            </label>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-4">
        <NuxtLink
          to="/medications"
          class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </NuxtLink>
        <button
          type="submit"
          :disabled="saving"
          class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
        >
          {{ saving ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import type { Medication } from '~/types'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const route = useRoute()
const router = useRouter()
const { get, update } = useMedications()
const { showToast } = useToast()
const { handleApiError } = useErrorHandler()

const medicationId = route.params.id as string
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const validationErrors = ref<Record<string, string[]> | null>(null)

const form = ref({
  name: '',
  generic_name: '',
  strength: '',
  dosage_form: '',
  category: '',
  is_active: true,
})

const loadMedication = async () => {
  loading.value = true
  error.value = ''

  const result = await get(medicationId)

  if (result.success && result.data) {
    const med = result.data
    form.value = {
      name: med.name,
      generic_name: med.generic_name || '',
      strength: med.strength || '',
      dosage_form: med.dosage_form || '',
      category: med.category || '',
      is_active: med.is_active,
    }
  } else {
    error.value = result.error || 'Failed to load medication'
    handleApiError(result.error || 'Failed to load medication')
  }

  loading.value = false
}

const handleSubmit = async () => {
  saving.value = true
  error.value = ''
  validationErrors.value = null

  const result = await update(medicationId, form.value)

  if (result.success) {
    showToast('success', 'Medication updated successfully')
    router.push('/medications')
  } else {
    error.value = result.error || 'Failed to update medication'
    validationErrors.value = result.errors || null
    handleApiError(result.error || 'Failed to update medication')
  }

  saving.value = false
}

onMounted(() => {
  loadMedication()
})
</script>
