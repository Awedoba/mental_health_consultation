<template>
  <div>
    <!-- Quick Actions Bar -->
    <div class="mb-6">
      <div class="bg-white shadow rounded-lg p-4">
        <div class="flex flex-wrap gap-3">
          <NuxtLink
            to="/consultations/create"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            New Consultation
          </NuxtLink>
          <NuxtLink
            to="/patients/create"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Patient
          </NuxtLink>
          <NuxtLink
            to="/billings/create"
            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            New Billing
          </NuxtLink>
          <NuxtLink
            to="/prescriptions/create"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            New Prescription
          </NuxtLink>
          <NuxtLink
            to="/home-visits/create"
            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-sm font-medium"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            New Home Visit
          </NuxtLink>
          <NuxtLink
            to="/reports"
            class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm font-medium"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Generate Report
          </NuxtLink>
          <NuxtLink
            to="/medications"
            class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 text-sm font-medium"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            Manage Medications
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
      <!-- Total Patients -->
      <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-lg transition" @click="$router.push('/patients')">
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
                <dd v-if="!loading && stats" class="text-xs text-gray-500 mt-1">
                  {{ stats.insuredPatients || 0 }} insured • {{ stats.uninsuredPatients || 0 }} uninsured
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Consultations This Month -->
      <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-lg transition" @click="$router.push('/consultations')">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="rounded-md bg-indigo-500 p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Consultations</dt>
                <dd class="flex items-baseline">
                  <div v-if="loading" class="h-8 w-20 bg-gray-200 rounded animate-pulse"></div>
                  <div v-else class="text-2xl font-semibold text-gray-900">{{ stats?.consultationsThisMonth || 0 }}</div>
                </dd>
                <dd class="text-xs text-gray-500 mt-1">This month</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Home Visits This Month -->
      <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-lg transition" @click="$router.push('/home-visits')">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="rounded-md bg-orange-500 p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Home Visits</dt>
                <dd class="flex items-baseline">
                  <div v-if="loading" class="h-8 w-20 bg-gray-200 rounded animate-pulse"></div>
                  <div v-else class="text-2xl font-semibold text-gray-900">{{ stats?.homeVisitsThisMonth || 0 }}</div>
                </dd>
                <dd class="text-xs text-gray-500 mt-1">This month</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Active Prescriptions -->
      <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-lg transition" @click="$router.push('/prescriptions')">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="rounded-md bg-blue-500 p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Active Prescriptions</dt>
                <dd class="flex items-baseline">
                  <div v-if="loading" class="h-8 w-20 bg-gray-200 rounded animate-pulse"></div>
                  <div v-else class="text-2xl font-semibold text-gray-900">{{ stats?.activePrescriptions || 0 }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Revenue This Month -->
      <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-lg transition" @click="$router.push('/billings')">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="rounded-md bg-green-500 p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Revenue</dt>
                <dd class="flex items-baseline">
                  <div v-if="loading" class="h-8 w-20 bg-gray-200 rounded animate-pulse"></div>
                  <div v-else class="text-2xl font-semibold text-gray-900">₵{{ formatCurrency(stats?.totalRevenueThisMonth || 0) }}</div>
                </dd>
                <dd class="text-xs text-gray-500 mt-1">This month</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Payments -->
      <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-lg transition" @click="$router.push('/billings?payment_status=pending')">
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
                <dt class="text-sm font-medium text-gray-500 truncate">Pending Payments</dt>
                <dd class="flex items-baseline">
                  <div v-if="loading" class="h-8 w-20 bg-gray-200 rounded animate-pulse"></div>
                  <div v-else class="text-2xl font-semibold text-gray-900">₵{{ formatCurrency(stats?.pendingPaymentsAmount || 0) }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Upcoming Next Visits -->
      <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-lg transition" @click="$router.push('/reports/next-visits')">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="rounded-md bg-purple-500 p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Visits</dt>
                <dd class="flex items-baseline">
                  <div v-if="loading" class="h-8 w-20 bg-gray-200 rounded animate-pulse"></div>
                  <div v-else class="text-2xl font-semibold text-gray-900">{{ stats?.upcomingNextVisits || 0 }}</div>
                </dd>
                <dd class="text-xs text-gray-500 mt-1">Next 30 days</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Active Clinicians (Admin only) -->
      <div v-if="user?.role === 'admin'" class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-lg transition" @click="$router.push('/admin/users')">
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

    <!-- Alerts Section -->
    <div v-if="!loading && dashboardData && hasAlerts" class="mb-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Alerts & Notifications</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Overdue Payments -->
          <div v-if="dashboardData.alerts?.overduePayments?.length > 0" class="border-l-4 border-yellow-400 bg-yellow-50 p-4 rounded">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h4 class="text-sm font-medium text-yellow-800">Overdue Payments</h4>
                <p class="mt-1 text-sm text-yellow-700">{{ dashboardData.alerts.overduePayments.length }} payment(s) overdue</p>
                <NuxtLink to="/billings?payment_status=pending" class="mt-2 text-sm font-medium text-yellow-800 hover:text-yellow-900">View all →</NuxtLink>
              </div>
            </div>
          </div>

          <!-- Upcoming Visits -->
          <div v-if="dashboardData.alerts?.upcomingVisits?.length > 0" class="border-l-4 border-blue-400 bg-blue-50 p-4 rounded">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Upcoming Visits</h4>
                <p class="mt-1 text-sm text-blue-700">{{ dashboardData.alerts.upcomingVisits.length }} visit(s) in next 7 days</p>
                <NuxtLink to="/reports/next-visits" class="mt-2 text-sm font-medium text-blue-800 hover:text-blue-900">View all →</NuxtLink>
              </div>
            </div>
          </div>

          <!-- Expiring Prescriptions -->
          <div v-if="dashboardData.alerts?.expiringPrescriptions?.length > 0" class="border-l-4 border-orange-400 bg-orange-50 p-4 rounded">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h4 class="text-sm font-medium text-orange-800">Expiring Prescriptions</h4>
                <p class="mt-1 text-sm text-orange-700">{{ dashboardData.alerts.expiringPrescriptions.length }} prescription(s) expiring soon</p>
                <NuxtLink to="/prescriptions" class="mt-2 text-sm font-medium text-orange-800 hover:text-orange-900">View all →</NuxtLink>
              </div>
            </div>
          </div>

          <!-- Incomplete Consultations -->
          <div v-if="dashboardData.alerts?.incompleteConsultations?.length > 0" class="border-l-4 border-red-400 bg-red-50 p-4 rounded">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h4 class="text-sm font-medium text-red-800">Incomplete Consultations</h4>
                <p class="mt-1 text-sm text-red-700">{{ dashboardData.alerts.incompleteConsultations.length }} consultation(s) missing data</p>
                <NuxtLink to="/consultations" class="mt-2 text-sm font-medium text-red-800 hover:text-red-900">View all →</NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
      <!-- Service Activity Chart -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Service Activity (Last 6 Months)</h3>
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

      <!-- Revenue Chart -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue & Payments (Last 6 Months)</h3>
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

      <!-- NHIS Coverage Chart -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">NHIS Coverage</h3>
        <div v-if="!loading && dashboardData?.charts?.nhisCoverage" class="h-64 flex items-center justify-center">
          <div class="text-center">
            <div class="text-4xl font-bold text-indigo-600">{{ dashboardData.charts.nhisCoverage.insured }}%</div>
            <p class="text-sm text-gray-600 mt-2">Insured</p>
            <div class="text-4xl font-bold text-gray-400 mt-4">{{ dashboardData.charts.nhisCoverage.uninsured }}%</div>
            <p class="text-sm text-gray-600 mt-2">Uninsured</p>
          </div>
        </div>
        <div v-else class="h-64 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg">
          <div class="animate-pulse text-gray-400">Loading...</div>
        </div>
      </div>

      <!-- Payment Status Chart -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Status Distribution</h3>
        <div v-if="!loading && dashboardData?.charts?.paymentStatus" class="h-64">
          <div class="grid grid-cols-2 gap-4">
            <div class="text-center">
              <div class="text-3xl font-bold text-green-600">{{ dashboardData.charts.paymentStatus.paid }}%</div>
              <p class="text-sm text-gray-600 mt-1">Paid</p>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-yellow-600">{{ dashboardData.charts.paymentStatus.pending }}%</div>
              <p class="text-sm text-gray-600 mt-1">Pending</p>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-blue-600">{{ dashboardData.charts.paymentStatus.partial }}%</div>
              <p class="text-sm text-gray-600 mt-1">Partial</p>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-gray-600">{{ dashboardData.charts.paymentStatus.waived }}%</div>
              <p class="text-sm text-gray-600 mt-1">Waived</p>
            </div>
          </div>
        </div>
        <div v-else class="h-64 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg">
          <div class="animate-pulse text-gray-400">Loading...</div>
        </div>
      </div>
    </div>

    <!-- Recent Activity Tabs -->
    <div class="bg-white shadow rounded-lg mb-8">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
          <button
            @click="activeTab = 'consultations'"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === 'consultations'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            Consultations
          </button>
          <button
            @click="activeTab = 'billings'"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === 'billings'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            Billings
          </button>
          <button
            @click="activeTab = 'prescriptions'"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === 'prescriptions'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            Prescriptions
          </button>
          <button
            @click="activeTab = 'homeVisits'"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === 'homeVisits'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            Home Visits
          </button>
          <button
            @click="activeTab = 'nextVisits'"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === 'nextVisits'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            Upcoming Visits
          </button>
        </nav>
      </div>

      <div class="overflow-x-auto">
        <!-- Consultations Tab -->
        <div v-if="activeTab === 'consultations'" class="p-6">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clinician</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
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
              <tr v-else-if="!dashboardData?.recentConsultations || dashboardData.recentConsultations.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No recent consultations</td>
              </tr>
              <tr v-else v-for="consultation in dashboardData.recentConsultations" :key="consultation.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ consultation.patient_name || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(consultation.consultation_date) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ consultation.clinician_name || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 text-xs font-semibold leading-5 rounded-full',
                    consultation.is_locked ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                  ]">
                    {{ consultation.is_locked ? 'Completed' : 'In Progress' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900">
                  <NuxtLink :to="`/consultations/${consultation.id}`">View</NuxtLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Billings Tab -->
        <div v-if="activeTab === 'billings'" class="p-6">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="loading">
                <td colspan="6" class="px-6 py-4 text-center">
                  <div class="flex justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                  </div>
                </td>
              </tr>
              <tr v-else-if="!dashboardData?.recentBillings || dashboardData.recentBillings.length === 0">
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No recent billings</td>
              </tr>
              <tr v-else v-for="billing in dashboardData.recentBillings" :key="billing.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ billing.patient_name || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₵{{ formatCurrency(billing.amount) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 text-xs font-semibold leading-5 rounded-full',
                    billing.payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                    billing.payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    billing.payment_status === 'partial' ? 'bg-blue-100 text-blue-800' :
                    'bg-gray-100 text-gray-800'
                  ]">
                    {{ billing.payment_status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(billing.billing_date) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ billing.invoice_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900">
                  <NuxtLink :to="`/billings/${billing.id}`">View</NuxtLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Prescriptions Tab -->
        <div v-if="activeTab === 'prescriptions'" class="p-6">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medication</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosage</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="loading">
                <td colspan="6" class="px-6 py-4 text-center">
                  <div class="flex justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                  </div>
                </td>
              </tr>
              <tr v-else-if="!dashboardData?.recentPrescriptions || dashboardData.recentPrescriptions.length === 0">
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No recent prescriptions</td>
              </tr>
              <tr v-else v-for="prescription in dashboardData.recentPrescriptions" :key="prescription.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ prescription.patient_name || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ prescription.medication_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ prescription.dosage }} {{ prescription.frequency }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(prescription.prescription_date) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 text-xs font-semibold leading-5 rounded-full',
                    prescription.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                  ]">
                    {{ prescription.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900">
                  <NuxtLink :to="`/prescriptions/${prescription.id}`">View</NuxtLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Home Visits Tab -->
        <div v-if="activeTab === 'homeVisits'" class="p-6">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clinician</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
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
              <tr v-else-if="!dashboardData?.recentHomeVisits || dashboardData.recentHomeVisits.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No recent home visits</td>
              </tr>
              <tr v-else v-for="visit in dashboardData.recentHomeVisits" :key="visit.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ visit.patient_name || visit.client_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ visit.community_location }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(visit.visit_date) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ visit.clinician_name || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900">
                  <NuxtLink :to="`/home-visits/${visit.id}`">View</NuxtLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Next Visits Tab -->
        <div v-if="activeTab === 'nextVisits'" class="p-6">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Visit Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clinician</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
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
              <tr v-else-if="!dashboardData?.upcomingNextVisits || dashboardData.upcomingNextVisits.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No upcoming visits scheduled</td>
              </tr>
              <tr v-else v-for="visit in dashboardData.upcomingNextVisits" :key="visit.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ visit.patient_name || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(visit.next_visit_date) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ visit.next_visit_purpose || '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ visit.clinician_name || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900">
                  <NuxtLink v-if="visit.consultation_id" :to="`/consultations/${visit.consultation_id}`">View</NuxtLink>
                  <span v-else class="text-gray-400">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Quick Reports Section -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Reports</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <NuxtLink
          to="/reports/client-list"
          class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition"
        >
          <h4 class="font-medium text-gray-900">Client List</h4>
          <p class="text-sm text-gray-500 mt-1">Generate client list with demographics</p>
        </NuxtLink>
        <NuxtLink
          to="/reports/next-visits"
          class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition"
        >
          <h4 class="font-medium text-gray-900">Next Visit Report</h4>
          <p class="text-sm text-gray-500 mt-1">View upcoming scheduled visits</p>
        </NuxtLink>
        <NuxtLink
          to="/reports/total-cases"
          class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition"
        >
          <h4 class="font-medium text-gray-900">Total Cases</h4>
          <p class="text-sm text-gray-500 mt-1">Summary statistics and breakdowns</p>
        </NuxtLink>
        <NuxtLink
          to="/reports/trends"
          class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition"
        >
          <h4 class="font-medium text-gray-900">Trend Report</h4>
          <p class="text-sm text-gray-500 mt-1">Analyze trends over time</p>
        </NuxtLink>
        <NuxtLink
          to="/reports/consulting-data-query"
          class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition"
        >
          <h4 class="font-medium text-gray-900">Consulting Data Query</h4>
          <p class="text-sm text-gray-500 mt-1">Query consultation data with filters</p>
        </NuxtLink>
        <NuxtLink
          to="/reports"
          class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition"
        >
          <h4 class="font-medium text-gray-900">All Reports</h4>
          <p class="text-sm text-gray-500 mt-1">View all available reports</p>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

import type { DashboardResponse, DashboardStats, ApiResponse } from '~/types'

const { user, token, fetchUser } = useAuth()
const { handleApiError } = useErrorHandler()
const config = useRuntimeConfig()

const loading = ref(true)
const error = ref<string | null>(null)
const stats = ref<DashboardStats | null>(null)
const dashboardData = ref<DashboardResponse | null>(null)
const activeTab = ref<'consultations' | 'billings' | 'prescriptions' | 'homeVisits' | 'nextVisits'>('consultations')

// Computed
const hasAlerts = computed(() => {
  if (!dashboardData.value?.alerts) return false
  const alerts = dashboardData.value.alerts
  return (
    alerts.overduePayments?.length > 0 ||
    alerts.upcomingVisits?.length > 0 ||
    alerts.expiringPrescriptions?.length > 0 ||
    alerts.incompleteConsultations?.length > 0
  )
})

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
    
    dashboardData.value = data
    stats.value = data.stats
  } catch (err: any) {
    error.value = err.data?.error?.message || 'Failed to load dashboard data'
    handleApiError(err, 'Failed to load dashboard data')
    
    // Set default empty data
    stats.value = {
      totalPatients: 0,
      insuredPatients: 0,
      uninsuredPatients: 0,
      consultationsThisMonth: 0,
      homeVisitsThisMonth: 0,
      activePrescriptions: 0,
      totalPrescriptionsThisMonth: 0,
      totalRevenueThisMonth: 0,
      totalRevenueAllTime: 0,
      pendingPaymentsAmount: 0,
      upcomingNextVisits: 0,
      totalBillingsThisMonth: 0,
    }
    dashboardData.value = {
      stats: stats.value,
      recentConsultations: [],
      recentBillings: [],
      recentPrescriptions: [],
      recentHomeVisits: [],
      upcomingNextVisits: [],
      alerts: {
        overduePayments: [],
        upcomingVisits: [],
        expiringPrescriptions: [],
        incompleteConsultations: [],
      },
      charts: {
        serviceActivity: { labels: [], consultations: [], homeVisits: [], prescriptions: [] },
        revenue: { labels: [], totalRevenue: [], paid: [], pending: [] },
        nhisCoverage: { insured: 0, uninsured: 0 },
        paymentStatus: { paid: 0, pending: 0, partial: 0, waived: 0 },
      },
    }
  } finally {
    loading.value = false
  }
}

// Format helpers
const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-GH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

// Lifecycle
onMounted(async () => {
  if (!user.value) {
    await fetchUser()
  }
  await fetchDashboardData()
})
</script>
