<template>
  <div>
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Client List Report</h2>
        <p class="mt-1 text-sm text-gray-500">View and export client list.</p>
      </div>
      <NuxtLink to="/reports" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Back to Reports
      </NuxtLink>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:p-6 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Report Under Construction</h3>
        <p class="mt-1 text-sm text-gray-500">This report is coming soon.</p>
      </div>
    </div>
  </div>
</template>
