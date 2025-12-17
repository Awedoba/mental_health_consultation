<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Home Visits</h1>
      <NuxtLink
        to="/home-visits/create"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Home Visit
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
            placeholder="Client name, community, or contact..."
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @input="debouncedSearch"
          />
        </div>

        <!-- Date From -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
          <input
            v-model="filters.date_from"
            type="date"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @change="loadHomeVisits"
          />
        </div>

        <!-- Date To -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
          <input
            v-model="filters.date_to"
            type="date"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @change="loadHomeVisits"
          />
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
      <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading home visits...</LoadingSpinner>
      
      <FormError :error="error" />

      <EmptyState
        v-if="!loading && homeVisits.length === 0"
        title="No home visits found"
        description="Start by creating a new home visit or adjust your filters."
        action-label="New Home Visit"
        @action="$router.push('/home-visits/create')"
      />

      <div v-if="!loading && homeVisits.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Client Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Age / Sex
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Community/Location
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Contact
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Visit Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Clinician
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="visit in homeVisits" :key="visit.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ visit.client_name }}
                <span v-if="visit.patient" class="text-xs text-gray-500 block">
                  (Linked to patient)
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span v-if="visit.age">{{ visit.age }} years</span>
                <span v-if="visit.age && visit.sex"> / </span>
                <span v-if="visit.sex">{{ formatSex(visit.sex) }}</span>
                <span v-if="!visit.age && !visit.sex">-</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ visit.community_location }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ visit.contact }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(visit.visit_date) }}
                <span v-if="visit.visit_time" class="text-xs text-gray-400 block">
                  {{ formatTime(visit.visit_time) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ visit.clinician ? `${visit.clinician.first_name} ${visit.clinician.last_name}` : 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <NuxtLink
                  :to="`/home-visits/${visit.id}`"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  View
                </NuxtLink>
                <NuxtLink
                  :to="`/home-visits/${visit.id}/edit`"
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
      <div v-if="!loading && homeVisits.length > 0 && pagination" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
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
import type { HomeVisit } from '~/types'
import { useDebounceFn } from '@vueuse/core'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const { list } = useHomeVisits()
const { handleApiError } = useErrorHandler()

const homeVisits = ref<HomeVisit[]>([])
const loading = ref(true)
const error = ref('')
const pagination = ref<any>(null)

const filters = ref({
  search: '',
  date_from: '',
  date_to: '',
  page: 1,
})

const debouncedSearch = useDebounceFn(() => {
  filters.value.page = 1
  loadHomeVisits()
}, 500)

const loadHomeVisits = async () => {
  loading.value = true
  error.value = ''

  const result = await list({
    search: filters.value.search || undefined,
    date_from: filters.value.date_from || undefined,
    date_to: filters.value.date_to || undefined,
    page: filters.value.page,
  })

  if (result.success) {
    homeVisits.value = result.data
    pagination.value = result.meta?.pagination || null
  } else {
    error.value = result.error || 'Failed to load home visits'
    handleApiError(result.error || 'Failed to load home visits')
  }

  loading.value = false
}

const loadPage = (page: number) => {
  filters.value.page = page
  loadHomeVisits()
}

const clearFilters = () => {
  filters.value = {
    search: '',
    date_from: '',
    date_to: '',
    page: 1,
  }
  loadHomeVisits()
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

const formatTime = (timeString: string) => {
  if (!timeString) return ''
  // If already in HH:MM format, return as is
  if (typeof timeString === 'string' && /^\d{2}:\d{2}$/.test(timeString)) {
    return timeString
  }
  // Try to extract time from datetime string
  const time = new Date(`2000-01-01T${timeString}`)
  if (isNaN(time.getTime())) return timeString.substring(0, 5)
  return time.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' })
}

const formatSex = (sex: string) => {
  return sex.charAt(0).toUpperCase() + sex.slice(1)
}

onMounted(() => {
  loadHomeVisits()
})
</script>

