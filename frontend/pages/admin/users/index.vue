<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
      <NuxtLink
        to="/admin/users/create"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
      >
        Create User
      </NuxtLink>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading users...</LoadingSpinner>
      
      <FormError v-if="error" :error="error" />

      <EmptyState
        v-if="!loading && !error && users.length === 0"
        title="No users found"
        description="Start by creating a new user."
        action-label="Create User"
        @action="$router.push('/admin/users/create')"
      />

      <table v-if="!loading && users.length > 0" class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Username
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Role
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Status
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ user.username }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ user.first_name }} {{ user.last_name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="{
                  'bg-purple-100 text-purple-800': user.role === 'admin',
                  'bg-blue-100 text-blue-800': user.role === 'clinician',
                }"
                class="px-2 py-1 text-xs font-semibold rounded-full"
              >
                {{ user.role }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="{
                  'bg-green-100 text-green-800': user.is_active,
                  'bg-red-100 text-red-800': !user.is_active,
                }"
                class="px-2 py-1 text-xs font-semibold rounded-full"
              >
                {{ user.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <NuxtLink
                :to="`/admin/users/${user.id}`"
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
definePageMeta({
  middleware: ['auth', 'role'],
  layout: 'default',
})

import type { User } from '~/types'

const { list } = useUsers()
const { handleApiError } = useErrorHandler()

const users = ref<User[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

const loadUsers = async () => {
  loading.value = true
  error.value = null

  const result = await list()

  if (result.success) {
    users.value = result.data || []
  } else {
    error.value = result.error || 'Failed to load users'
    handleApiError({ data: { error: { message: result.error } } }, 'Failed to load users')
  }

  loading.value = false
}

onMounted(() => {
  loadUsers()
})
</script>

