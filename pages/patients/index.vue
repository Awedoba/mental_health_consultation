<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Patients</h1>
      <NuxtLink
        to="/patients/create"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
      >
        Add New Patient
      </NuxtLink>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <div class="mb-4">
        <input
          v-model="search"
          type="text"
          placeholder="Search patients..."
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          aria-label="Search patients"
          @input="debouncedSearch"
        />
      </div>

      <div v-if="loading" class="text-center py-8">Loading...</div>
      <div v-else-if="error" class="text-red-600 py-4">{{ error }}</div>
      <div v-else-if="patients.length === 0" class="text-center py-8 text-gray-500">
        No patients found
      </div>
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              DOB
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Phone
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="patient in patients" :key="patient.id">
            <td class="px-6 py-4 whitespace-nowrap">
              {{ patient.last_name }}, {{ patient.first_name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ new Date(patient.date_of_birth).toLocaleDateString() }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">{{ patient.phone_number }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <NuxtLink
                :to="`/patients/${patient.id}`"
                class="text-indigo-600 hover:text-indigo-900"
              >
                View
              </NuxtLink>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Patient } from '~/types'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const { list } = usePatients()
const { handleApiError } = useErrorHandler()

const patients = ref<Patient[]>([])
const loading = ref(false)
const error = ref('')
const search = ref('')

let searchTimeout: ReturnType<typeof setTimeout> | null = null

const loadPatients = async () => {
  loading.value = true
  error.value = ''

  const result = await list({
    search: search.value || undefined,
    is_active: true,
  })

  if (result.success) {
    patients.value = result.data || []
  } else {
    error.value = result.error || 'Failed to load patients'
    handleApiError({ data: { error: { message: result.error } } }, 'Failed to load patients')
  }

  loading.value = false
}

const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  searchTimeout = setTimeout(() => {
    loadPatients()
  }, 300)
}

onMounted(() => {
  loadPatients()
})

onUnmounted(() => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
})
</script>

