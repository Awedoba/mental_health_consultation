<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Prescriptions</h1>
      <NuxtLink
        to="/prescriptions/create"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Prescription
      </NuxtLink>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Medication name..."
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @input="debouncedSearch"
          />
        </div>

        <!-- Active Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            v-model="filters.is_active"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @change="loadPrescriptions"
          >
            <option :value="undefined">All</option>
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </select>
        </div>

        <!-- Clear Filters -->
        <div class="flex items-end">
          <button
            @click="clearFilters"
            class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50"
          >
            Clear Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="bg-white shadow rounded-lg">
      <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading prescriptions...</LoadingSpinner>
      
      <FormError :error="error" />

      <EmptyState
        v-if="!loading && prescriptions.length === 0"
        title="No prescriptions found"
        description="Start by creating a new prescription or adjust your filters."
        action-label="New Prescription"
        @action="$router.push('/prescriptions/create')"
      />

      <div v-if="!loading && prescriptions.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Patient
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Medication
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Dosage
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Frequency
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Duration
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="prescription in prescriptions" :key="prescription.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ prescription.patient ? `${prescription.patient.first_name} ${prescription.patient.last_name}` : 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ prescription.medication?.name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ prescription.dosage }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ prescription.frequency }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ prescription.duration }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(prescription.prescription_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'bg-green-100 text-green-800': prescription.is_active,
                    'bg-gray-100 text-gray-800': !prescription.is_active,
                  }"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                >
                  {{ prescription.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <NuxtLink
                  :to="`/prescriptions/${prescription.id}`"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  View
                </NuxtLink>
                <NuxtLink
                  :to="`/prescriptions/${prescription.id}/edit`"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  Edit
                </NuxtLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && prescriptions.length > 0 && pagination" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
          </div>
          <div class="flex gap-2">
            <button
              @click="loadPage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="px-3 py-2 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            <button
              @click="loadPage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="px-3 py-2 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Prescription } from '~/types'
import { useDebounceFn } from '@vueuse/core'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const { list } = usePrescriptions()
const { handleApiError } = useErrorHandler()

const prescriptions = ref<Prescription[]>([])
const loading = ref(true)
const error = ref('')
const pagination = ref<any>(null)

const filters = ref({
  search: '',
  is_active: undefined as boolean | undefined,
  page: 1,
})

const debouncedSearch = useDebounceFn(() => {
  filters.value.page = 1
  loadPrescriptions()
}, 500)

const loadPrescriptions = async () => {
  loading.value = true
  error.value = ''

  const result = await list({
    search: filters.value.search || undefined,
    is_active: filters.value.is_active,
    page: filters.value.page,
  })

  if (result.success) {
    prescriptions.value = result.data
    pagination.value = result.meta?.pagination || null
  } else {
    error.value = result.error || 'Failed to load prescriptions'
    handleApiError(result.error || 'Failed to load prescriptions')
  }

  loading.value = false
}

const loadPage = (page: number) => {
  filters.value.page = page
  loadPrescriptions()
}

const clearFilters = () => {
  filters.value = {
    search: '',
    is_active: undefined,
    page: 1,
  }
  loadPrescriptions()
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  loadPrescriptions()
})
</script>

