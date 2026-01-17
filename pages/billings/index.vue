<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Billings</h1>
      <NuxtLink
        to="/billings/create"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Billing
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
            placeholder="Invoice number or patient name..."
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @input="debouncedSearch"
          />
        </div>

        <!-- Payment Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
          <select
            v-model="filters.payment_status"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @change="loadBillings"
          >
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="partial">Partial</option>
            <option value="paid">Paid</option>
            <option value="waived">Waived</option>
          </select>
        </div>

        <!-- Service Type Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
          <select
            v-model="filters.service_type"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
            @change="loadBillings"
          >
            <option value="">All</option>
            <option value="consultation">Consultation</option>
            <option value="home_visit">Home Visit</option>
            <option value="medication">Medication</option>
            <option value="other">Other</option>
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
      <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading billings...</LoadingSpinner>
      
      <FormError :error="error" />

      <EmptyState
        v-if="!loading && billings.length === 0"
        title="No billings found"
        description="Start by creating a new billing or adjust your filters."
        action-label="New Billing"
        @action="$router.push('/billings/create')"
      />

      <div v-if="!loading && billings.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Invoice #
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Patient
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Service Type
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Amount
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Payment Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="billing in billings" :key="billing.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ billing.invoice_number }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ billing.patient ? `${billing.patient.first_name} ${billing.patient.last_name}` : 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(billing.billing_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatServiceType(billing.service_type) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ formatCurrency(billing.amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800': billing.payment_status === 'pending',
                    'bg-blue-100 text-blue-800': billing.payment_status === 'partial',
                    'bg-green-100 text-green-800': billing.payment_status === 'paid',
                    'bg-gray-100 text-gray-800': billing.payment_status === 'waived',
                  }"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                >
                  {{ formatPaymentStatus(billing.payment_status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <NuxtLink
                  :to="`/billings/${billing.id}`"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  View
                </NuxtLink>
                <NuxtLink
                  :to="`/billings/${billing.id}/edit`"
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
      <div v-if="!loading && billings.length > 0 && pagination" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
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
import type { Billing } from '~/types'
import { useDebounceFn } from '@vueuse/core'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const { list } = useBillings()
const { handleApiError } = useErrorHandler()

const billings = ref<Billing[]>([])
const loading = ref(true)
const error = ref('')
const pagination = ref<any>(null)

const filters = ref({
  search: '',
  payment_status: '',
  service_type: '',
  page: 1,
})

const debouncedSearch = useDebounceFn(() => {
  filters.value.page = 1
  loadBillings()
}, 500)

const loadBillings = async () => {
  loading.value = true
  error.value = ''

  const result = await list({
    search: filters.value.search || undefined,
    payment_status: filters.value.payment_status || undefined,
    service_type: filters.value.service_type || undefined,
    page: filters.value.page,
  })

  if (result.success) {
    billings.value = result.data
    pagination.value = result.meta?.pagination || null
  } else {
    error.value = result.error || 'Failed to load billings'
    handleApiError(result.error || 'Failed to load billings')
  }

  loading.value = false
}

const loadPage = (page: number) => {
  filters.value.page = page
  loadBillings()
}

const clearFilters = () => {
  filters.value = {
    search: '',
    payment_status: '',
    service_type: '',
    page: 1,
  }
  loadBillings()
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount)
}

const formatServiceType = (type: string) => {
  return type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const formatPaymentStatus = (status: string) => {
  return status.charAt(0).toUpperCase() + status.slice(1)
}

onMounted(() => {
  loadBillings()
})
</script>

