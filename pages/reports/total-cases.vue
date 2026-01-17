<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Total Cases Report</h1>
      <button
        @click="generateReport"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
      >
        Generate Report
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
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

      <div v-if="!loading && reportData" class="p-6">
        <!-- Summary Statistics -->
        <div class="mb-8">
          <h2 class="text-xl font-semibold mb-4">Summary Statistics</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-sm text-gray-600">Total Patients</div>
              <div class="text-2xl font-bold text-gray-900">{{ reportData.summary?.total_patients || 0 }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-sm text-gray-600">New Patients</div>
              <div class="text-2xl font-bold text-gray-900">{{ reportData.summary?.new_patients || 0 }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-sm text-gray-600">Total Consultations</div>
              <div class="text-2xl font-bold text-gray-900">{{ reportData.summary?.total_consultations || 0 }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-sm text-gray-600">Home Visits</div>
              <div class="text-2xl font-bold text-gray-900">{{ reportData.summary?.total_home_visits || 0 }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-sm text-gray-600">Total Revenue</div>
              <div class="text-2xl font-bold text-gray-900">₵{{ formatCurrency(reportData.summary?.total_revenue || 0) }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-sm text-gray-600">Pending Payments</div>
              <div class="text-2xl font-bold text-gray-900">₵{{ formatCurrency(reportData.summary?.pending_payments || 0) }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-sm text-gray-600">Unique Patients Consulted</div>
              <div class="text-2xl font-bold text-gray-900">{{ reportData.summary?.unique_patients_consulted || 0 }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-sm text-gray-600">Total Billings</div>
              <div class="text-2xl font-bold text-gray-900">{{ reportData.summary?.total_billings || 0 }}</div>
            </div>
          </div>
        </div>

        <!-- Breakdowns -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Consultations by Type -->
          <div>
            <h3 class="text-lg font-semibold mb-3">Consultations by Type</h3>
            <div class="space-y-2">
              <div
                v-for="item in reportData.breakdown?.consultations_by_type || []"
                :key="item.session_type"
                class="flex justify-between items-center p-2 bg-gray-50 rounded"
              >
                <span class="text-sm text-gray-700">{{ item.session_type || 'N/A' }}</span>
                <span class="text-sm font-semibold text-gray-900">{{ item.count }}</span>
              </div>
            </div>
          </div>

          <!-- Patients by NHIS Status -->
          <div>
            <h3 class="text-lg font-semibold mb-3">Patients by NHIS Status</h3>
            <div class="space-y-2">
              <div
                v-for="item in reportData.breakdown?.patients_by_nhis || []"
                :key="item.nhis_status"
                class="flex justify-between items-center p-2 bg-gray-50 rounded"
              >
                <span class="text-sm text-gray-700">{{ item.nhis_status || 'N/A' }}</span>
                <span class="text-sm font-semibold text-gray-900">{{ item.count }}</span>
              </div>
            </div>
          </div>

          <!-- Billings by Status -->
          <div>
            <h3 class="text-lg font-semibold mb-3">Billings by Status</h3>
            <div class="space-y-2">
              <div
                v-for="item in reportData.breakdown?.billings_by_status || []"
                :key="item.payment_status"
                class="flex justify-between items-center p-2 bg-gray-50 rounded"
              >
                <span class="text-sm text-gray-700">{{ item.payment_status || 'N/A' }}</span>
                <div class="text-right">
                  <div class="text-sm font-semibold text-gray-900">{{ item.count }}</div>
                  <div class="text-xs text-gray-500">₵{{ formatCurrency(item.total || 0) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const { totalCases } = useReports()

const loading = ref(false)
const error = ref('')
const reportData = ref<any>(null)

const filters = ref({
  date_from: new Date(new Date().getFullYear(), 0, 1).toISOString().split('T')[0],
  date_to: new Date().toISOString().split('T')[0],
})

const generateReport = async () => {
  loading.value = true
  error.value = ''

  const result = await totalCases({
    date_from: filters.value.date_from || undefined,
    date_to: filters.value.date_to || undefined,
  })

  if (result.success) {
    reportData.value = result.data
  } else {
    error.value = result.error || 'Failed to generate report'
  }

  loading.value = false
}

const clearFilters = () => {
  filters.value = {
    date_from: new Date(new Date().getFullYear(), 0, 1).toISOString().split('T')[0],
    date_to: new Date().toISOString().split('T')[0],
  }
  generateReport()
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-GH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

onMounted(() => {
  generateReport()
})
</script>

