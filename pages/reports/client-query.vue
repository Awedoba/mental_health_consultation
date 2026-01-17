<template>
  <div>
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Client Query Report</h2>
        <p class="mt-1 text-sm text-gray-500">Custom query builder for client data.</p>
      </div>
      <NuxtLink to="/reports" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Back to Reports
      </NuxtLink>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:p-6 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Report Under Construction</h3>
        <p class="mt-1 text-sm text-gray-500">This report is coming soon.</p>
      </div>
    </div>
  </div>
</template>
