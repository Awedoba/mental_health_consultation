<template>
  <div>
    <!-- Stats grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
      <!-- Total Patients -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="rounded-md bg-indigo-500 p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Patients</dt>
                <dd class="flex items-baseline">
                  <div v-if="loading" class="h-8 w-20 bg-gray-200 rounded animate-pulse"></div>
                  <div v-else class="text-2xl font-semibold text-gray-900">{{ stats?.totalPatients || 0 }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Consultations This Month -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="rounded-md bg-green-500 p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                <dd class="flex items-baseline">
                  <div v-if="loading" class="h-8 w-20 bg-gray-200 rounded animate-pulse"></div>
                  <div v-else class="text-2xl font-semibold text-gray-900">{{ stats?.consultationsThisMonth || 0 }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Reviews -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="rounded-md bg-yellow-500 p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Pending Reviews</dt>
                <dd class="flex items-baseline">
                  <div v-if="loading" class="h-8 w-20 bg-gray-200 rounded animate-pulse"></div>
                  <div v-else class="text-2xl font-semibold text-gray-900">{{ stats?.pendingReviews || 0 }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Active Clinicians (Admin only) -->
      <div v-if="user?.role === 'admin'" class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="rounded-md bg-purple-500 p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Active Clinicians</dt>
                <dd class="flex items-baseline">
                  <div v-if="loading" class="h-8 w-20 bg-gray-200 rounded animate-pulse"></div>
                  <div v-else class="text-2xl font-semibold text-gray-900">{{ stats?.activeClinicians || 0 }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts section -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
      <!-- Chart placeholder -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Consultations Trend</h3>
        <div class="h-64 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg">
          <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">Chart visualization placeholder</p>
            <p class="text-xs text-gray-400">Install Chart.js for interactive charts</p>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Risk Assessment Distribution</h3>
        <div class="h-64 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg">
          <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">Chart visualization placeholder</p>
            <p class="text-xs text-gray-400">Install Chart.js for interactive charts</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Recent Consultations</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clinician</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td colspan="5" class="px-6 py-4 text-center">
                <div class="flex justify-center">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                </div>
              </td>
            </tr>
            <tr v-else-if="!recentConsultations || recentConsultations.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                No recent consultations found
              </td>
            </tr>
            <tr v-else v-for="consultation in recentConsultations" :key="consultation.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ consultation.patient_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(consultation.consultation_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ consultation.clinician_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'inline-flex px-2 text-xs font-semibold leading-5 rounded-full',
                  consultation.is_locked ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                ]">
                  {{ consultation.is_locked ? 'Completed' : 'In Progress' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900">
                <NuxtLink :to="`/consultations/${consultation.id}`" class="font-medium">View</NuxtLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

import type { DashboardStats, DashboardResponse, Consultation, ApiResponse } from '~/types'

const { user, token, fetchUser } = useAuth()
const { handleApiError } = useErrorHandler()
const config = useRuntimeConfig()

const loading = ref(true)
const error = ref<string | null>(null)
const stats = ref<DashboardStats | null>(null)
const recentConsultations = ref<Consultation[]>([])

// Fetch dashboard data
const fetchDashboardData = async () => {
  loading.value = true
  error.value = null
  
  try {
    const { data } = await $fetch<ApiResponse<DashboardResponse>>(`${config.public.apiBase}/dashboard`, {
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
    })
    
    stats.value = data.stats
    recentConsultations.value = data.recentConsultations || []
  } catch (err: any) {
    error.value = err.data?.error?.message || 'Failed to load dashboard data'
    handleApiError(err, 'Failed to load dashboard data')
    
    // Set default empty data
    stats.value = {
      totalPatients: 0,
      consultationsThisMonth: 0,
      pendingReviews: 0,
      activeClinicians: 0,
    }
    recentConsultations.value = []
  } finally {
    loading.value = false
  }
}

// Retry function
const retry = () => {
  fetchDashboardData()
}

// Format date helper
const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

// Lifecycle
onMounted(async () => {
  if (!user.value) {
    await fetchUser()
  }
  await fetchDashboardData()
})
</script>
