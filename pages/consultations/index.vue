<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Consultations</h1>
      <NuxtLink
        to="/consultations/create"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Consultation
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
            placeholder="Patient or clinician name..."
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @input="debouncedSearch"
          />
        </div>

        <!-- Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            v-model="filters.status"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @change="loadConsultations"
          >
            <option value="">All</option>
            <option value="unlocked">In Progress</option>
            <option value="locked">Completed</option>
          </select>
        </div>

        <!-- Risk Assessment Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Risk Level</label>
          <select
            v-model="filters.risk_assessment"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @change="loadConsultations"
          >
            <option value="">All</option>
            <option value="low">Low</option>
            <option value="moderate">Moderate</option>
            <option value="high">High</option>
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
      <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading consultations...</LoadingSpinner>
      
      <FormError :error="error" />

      <EmptyState
        v-if="!loading && consultations.length === 0"
        title="No consultations found"
        description="Start by creating a new consultation or adjust your filters."
        action-label="New Consultation"
        @action="$router.push('/consultations/create')"
      >
        <template #icon>
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </template>
      </EmptyState>

      <div v-if="!loading && consultations.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Patient
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date & Time
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Clinician
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Session Type
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Risk Level
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
            <tr
              v-for="consultation in consultations"
              :key="consultation.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">
                  {{ consultation.patient_name || 'N/A' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ formatDate(consultation.consultation_date) }}
                </div>
                <div class="text-sm text-gray-500">
                  {{ formatTime(consultation.consultation_time) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ consultation.clinician_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ consultation.session_type }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'inline-flex px-2 text-xs font-semibold leading-5 rounded-full',
                    getRiskColor(consultation.risk_assessment)
                  ]"
                >
                  {{ consultation.risk_assessment }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'inline-flex px-2 text-xs font-semibold leading-5 rounded-full',
                    consultation.is_locked ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                  ]"
                >
                  {{ consultation.is_locked ? 'Completed' : 'In Progress' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <NuxtLink
                  :to="`/consultations/${consultation.id}`"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  View
                </NuxtLink>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="meta?.pagination" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="loadPage((meta.pagination.page || meta.pagination.current_page) - 1)"
              :disabled="(meta.pagination.page || meta.pagination.current_page) <= 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
            >
              Previous
            </button>
            <button
              @click="loadPage((meta.pagination.page || meta.pagination.current_page) + 1)"
              :disabled="(meta.pagination.page || meta.pagination.current_page) >= (meta.pagination.total_pages || meta.pagination.last_page)"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
            >
              Next
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ meta.pagination.from || ((meta.pagination.page || meta.pagination.current_page || 1) - 1) * meta.pagination.per_page + 1 }}</span>
                to
                <span class="font-medium">{{ meta.pagination.to || Math.min((meta.pagination.page || meta.pagination.current_page || 1) * meta.pagination.per_page, meta.pagination.total || 0) }}</span>
                of
                <span class="font-medium">{{ meta.pagination.total || 0 }}</span>
                results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                <button
                  @click="loadPage((meta.pagination.page || meta.pagination.current_page) - 1)"
                  :disabled="(meta.pagination.page || meta.pagination.current_page) <= 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                >
                  <span class="sr-only">Previous</span>
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
                <button
                  @click="loadPage((meta.pagination.page || meta.pagination.current_page) + 1)"
                  :disabled="(meta.pagination.page || meta.pagination.current_page) >= (meta.pagination.total_pages || meta.pagination.last_page)"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                >
                  <span class="sr-only">Next</span>
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>
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

const { list } = useConsultations()
const { handleApiError } = useErrorHandler()

const consultations = ref<Consultation[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const meta = ref<any>(null)

const filters = reactive({
  search: '',
  status: '',
  risk_assessment: '',
  page: 1,
  per_page: 20,
})

let searchTimeout: number

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadConsultations()
  }, 500)
}

const loadConsultations = async () => {
  loading.value = true
  error.value = null

  const result = await list({
    search: filters.search || undefined,
    status: filters.status as any || undefined,
    risk_assessment: filters.risk_assessment as any || undefined,
    page: filters.page,
    per_page: filters.per_page,
  })

  if (result.success) {
    consultations.value = result.data
    meta.value = result.meta
  } else {
    error.value = result.error || 'Failed to load consultations'
    handleApiError({ data: { error: { message: result.error } } }, 'Failed to load consultations')
  }

  loading.value = false
}

const loadPage = (page: number) => {
  filters.page = page
  loadConsultations()
}

const clearFilters = () => {
  filters.search = ''
  filters.status = ''
  filters.risk_assessment = ''
  filters.page = 1
  loadConsultations()
}

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  try {
    // Handle both date strings and date objects
    const date = typeof dateString === 'string' ? new Date(dateString) : dateString
    if (isNaN(date.getTime())) return 'N/A'
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  } catch {
    return 'N/A'
  }
}

const formatTime = (timeString: string) => {
  if (!timeString) return 'N/A'
  try {
    // Handle time strings in various formats
    let time = timeString
    // If it's a full datetime string, extract just the time part
    if (timeString.includes('T')) {
      time = timeString.split('T')[1]?.split('.')[0] || timeString
    }
    // If it's in HH:MM:SS format, extract HH:MM
    if (time.includes(':')) {
      const parts = time.split(':')
      return `${parts[0]}:${parts[1]}`
    }
    return time
  } catch {
    return 'N/A'
  }
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
  loadConsultations()
})

onUnmounted(() => {
  clearTimeout(searchTimeout)
})
</script>
