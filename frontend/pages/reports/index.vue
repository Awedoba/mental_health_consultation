<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Reports</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Client List Report -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">Client List</h2>
        <p class="text-gray-600 text-sm mb-4">Generate a list of all clients with demographics and NHIS status.</p>
        <NuxtLink
          to="/reports/client-list"
          class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
        >
          Generate Report
        </NuxtLink>
      </div>

      <!-- Client Query Report -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">Client Query</h2>
        <p class="text-gray-600 text-sm mb-4">Advanced client search with multiple filters.</p>
        <NuxtLink
          to="/reports/client-query"
          class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
        >
          Generate Report
        </NuxtLink>
      </div>

      <!-- Consulting History Report -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">Consulting History</h2>
        <p class="text-gray-600 text-sm mb-4">View all consultations for a specific client.</p>
        <NuxtLink
          to="/reports/consulting-history"
          class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
        >
          Generate Report
        </NuxtLink>
      </div>

      <!-- Next Visit Report -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">Next Visit Report</h2>
        <p class="text-gray-600 text-sm mb-4">View upcoming scheduled visits.</p>
        <NuxtLink
          to="/reports/next-visits"
          class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
        >
          Generate Report
        </NuxtLink>
      </div>

      <!-- Consulting Data Query Report -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">Consulting Data Query</h2>
        <p class="text-gray-600 text-sm mb-4">Query consultation data with advanced filters.</p>
        <NuxtLink
          to="/reports/consulting-data-query"
          class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
        >
          Generate Report
        </NuxtLink>
      </div>

      <!-- Trend Report -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">Trend Report</h2>
        <p class="text-gray-600 text-sm mb-4">Analyze consultation trends over time with weight tracking.</p>
        <NuxtLink
          to="/reports/trends"
          class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
        >
          Generate Report
        </NuxtLink>
      </div>

      <!-- Total Cases Report -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-2">Total Cases</h2>
        <p class="text-gray-600 text-sm mb-4">Summary statistics and breakdowns.</p>
        <NuxtLink
          to="/reports/total-cases"
          class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
        >
          Generate Report
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
</script>

