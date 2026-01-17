<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Next Visit Report</h1>
      <button
        @click="generateReport"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
      >
        Generate Report
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
          <input
            v-model="filters.date_from"
            type="date"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
          <input
            v-model="filters.date_to"
            type="date"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Clinician</label>
          <select
            v-model="filters.clinician_id"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
          >
            <option value="">All Clinicians</option>
            <option v-for="clinician in clinicians" :key="clinician.id" :value="clinician.id">
              {{ clinician.first_name }} {{ clinician.last_name }}
            </option>
          </select>
        </div>
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

    <!-- Report Content -->
    <div class="bg-white shadow rounded-lg">
      <LoadingSpinner v-if="loading" size="lg" class="py-12">Generating report...</LoadingSpinner>
      
      <FormError :error="error" />

      <EmptyState
        v-if="!loading && nextVisits.length === 0"
        title="No upcoming visits found"
        description="No scheduled visits match your criteria."
      />

      <div v-if="!loading && nextVisits.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Patient
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Next Visit Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Purpose
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
            <tr v-for="visit in nextVisits" :key="visit.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                <template v-if="visit.consultation?.patient">
                  {{ visit.consultation.patient.last_name }}, {{ visit.consultation.patient.first_name }}
                </template>
                <span v-else class="text-gray-400">N/A</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(visit.next_visit_date) }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ visit.next_visit_purpose || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <template v-if="visit.consultation?.primary_clinician">
                  {{ visit.consultation.primary_clinician.first_name }} {{ visit.consultation.primary_clinician.last_name }}
                </template>
                <span v-else class="text-gray-400">N/A</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <NuxtLink
                  v-if="visit.consultation_id"
                  :to="`/consultations/${visit.consultation_id}`"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  View Consultation
                </NuxtLink>
                <span v-else class="text-gray-400">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { User } from '~/types'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const { nextVisits: getNextVisits } = useReports()
const { list: listUsers } = useUsers()

const loading = ref(false)
const error = ref('')
const nextVisits = ref<any[]>([])
const clinicians = ref<User[]>([])

const filters = ref({
  date_from: new Date().toISOString().split('T')[0],
  date_to: '',
  clinician_id: '',
})

const generateReport = async () => {
  loading.value = true
  error.value = ''

  const result = await getNextVisits({
    date_from: filters.value.date_from || undefined,
    date_to: filters.value.date_to || undefined,
    clinician_id: filters.value.clinician_id || undefined,
  })

  if (result.success) {
    nextVisits.value = result.data || []
  } else {
    error.value = result.error || 'Failed to generate report'
  }

  loading.value = false
}

const clearFilters = () => {
  filters.value = {
    date_from: new Date().toISOString().split('T')[0],
    date_to: '',
    clinician_id: '',
  }
  generateReport()
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(async () => {
  // Load clinicians
  const usersResult = await listUsers({ role: 'clinician' })
  if (usersResult.success) {
    clinicians.value = usersResult.data || []
  }
  
  // Generate initial report
  await generateReport()
})
</script>

