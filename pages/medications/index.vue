<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Medications</h1>
      <NuxtLink
        to="/medications/create"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Medication
      </NuxtLink>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Search -->
        <div class="lg:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search by name or generic name..."
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
            @change="loadMedications"
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
      <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading medications...</LoadingSpinner>
      
      <FormError :error="error" />

      <EmptyState
        v-if="!loading && medications.length === 0"
        title="No medications found"
        description="Start by adding a new medication or adjust your filters."
        action-label="Add Medication"
        @action="$router.push('/medications/create')"
      />

      <div v-if="!loading && medications.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Generic Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Strength
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Dosage Form
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Category
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
            <tr v-for="medication in medications" :key="medication.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ medication.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ medication.generic_name || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ medication.strength || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ medication.dosage_form || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ medication.category || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'bg-green-100 text-green-800': medication.is_active,
                    'bg-gray-100 text-gray-800': !medication.is_active,
                  }"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                >
                  {{ medication.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <NuxtLink
                  :to="`/medications/${medication.id}/edit`"
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
      <div v-if="!loading && medications.length > 0 && pagination" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
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
import type { Medication } from '~/types'
import { useDebounceFn } from '@vueuse/core'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const { list } = useMedications()
const { handleApiError } = useErrorHandler()

const medications = ref<Medication[]>([])
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
  loadMedications()
}, 500)

const loadMedications = async () => {
  loading.value = true
  error.value = ''

  const result = await list({
    search: filters.value.search || undefined,
    is_active: filters.value.is_active,
    page: filters.value.page,
  })

  if (result.success) {
    medications.value = result.data
    pagination.value = result.meta?.pagination || null
  } else {
    error.value = result.error || 'Failed to load medications'
    handleApiError(result.error || 'Failed to load medications')
  }

  loading.value = false
}

const loadPage = (page: number) => {
  filters.value.page = page
  loadMedications()
}

const clearFilters = () => {
  filters.value = {
    search: '',
    is_active: undefined,
    page: 1,
  }
  loadMedications()
}

onMounted(() => {
  loadMedications()
})
</script>
