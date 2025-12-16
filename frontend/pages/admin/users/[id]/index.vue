<template>
  <div>
    <div class="mb-6">
      <NuxtLink
        to="/admin/users"
        class="text-indigo-600 hover:text-indigo-900 flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Users
      </NuxtLink>
    </div>

    <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading user...</LoadingSpinner>
    
    <FormError :error="error" />

    <div v-if="!loading && user" class="space-y-6">
      <!-- Header -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              {{ user.first_name }} {{ user.last_name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">@{{ user.username }}</p>
          </div>
          <div class="flex gap-2">
            <NuxtLink
              :to="`/admin/users/${user.id}/edit`"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit
            </NuxtLink>
            <button
              @click="confirmToggleActive = true"
              :class="[
                'inline-flex items-center px-4 py-2 border shadow-sm text-sm font-medium rounded-md',
                user.is_active
                  ? 'border-red-300 text-red-700 bg-white hover:bg-red-50'
                  : 'border-green-300 text-green-700 bg-white hover:bg-green-50'
              ]"
            >
              {{ user.is_active ? 'Deactivate' : 'Activate' }}
            </button>
          </div>
        </div>

        <!-- Status Badge -->
        <div class="mt-4">
          <span
            :class="[
              'inline-flex px-3 py-1 text-sm font-semibold rounded-full',
              user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
            ]"
          >
            {{ user.is_active ? 'Active' : 'Inactive' }}
          </span>
          <span
            :class="[
              'ml-2 inline-flex px-3 py-1 text-sm font-semibold rounded-full',
              user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'
            ]"
          >
            {{ user.role === 'admin' ? 'Administrator' : 'Clinician' }}
          </span>
        </div>
      </div>

      <!-- User Details -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">User Information</h2>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
          <div>
            <dt class="text-sm font-medium text-gray-500">Email</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ user.email }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Phone</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ user.phone || 'N/A' }}</dd>
          </div>
          <div v-if="user.professional_title">
            <dt class="text-sm font-medium text-gray-500">Professional Title</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ user.professional_title }}</dd>
          </div>
          <div v-if="user.license_number">
            <dt class="text-sm font-medium text-gray-500">License Number</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ user.license_number }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Account Created</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(user.created_at) }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(user.updated_at) }}</dd>
          </div>
          <div v-if="user.last_login">
            <dt class="text-sm font-medium text-gray-500">Last Login</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(user.last_login) }}</dd>
          </div>
        </dl>
      </div>

      <!-- Activity Statistics (placeholder) -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Activity Statistics</h2>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
          <div class="bg-gray-50 rounded-lg p-4">
            <dt class="text-sm font-medium text-gray-500">Total Consultations</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">-</dd>
            <p class="mt-1 text-xs text-gray-500">Coming soon</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-4">
            <dt class="text-sm font-medium text-gray-500">Patients Seen</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">-</dd>
            <p class="mt-1 text-xs text-gray-500">Coming soon</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-4">
            <dt class="text-sm font-medium text-gray-500">Avg. Session Duration</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">-</dd>
            <p class="mt-1 text-xs text-gray-500">Coming soon</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <ConfirmModal
      v-model="confirmToggleActive"
      :title="user?.is_active ? 'Deactivate User' : 'Activate User'"
      :message="user?.is_active 
        ? 'Are you sure you want to deactivate this user? They will no longer be able to access the system.' 
        : 'Are you sure you want to activate this user? They will regain access to the system.'"
      :confirm-label="user?.is_active ? 'Deactivate' : 'Activate'"
      :danger="user?.is_active"
      :loading="toggling"
      @confirm="handleToggleActive"
    />
  </div>
</template>

<script setup lang="ts">
import type { User } from '~/types'

definePageMeta({
  middleware: ['auth', 'role'],
  layout: 'default',
})

const route = useRoute()
const router = useRouter()
const { show, toggleActive } = useUsers()
const { handleApiError, showSuccess } = useErrorHandler()

const user = ref<User | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const confirmToggleActive = ref(false)
const toggling = ref(false)

const loadUser = async () => {
  loading.value = true
  error.value = null

  const result = await show(route.params.id as string)

  if (result.success && result.data) {
    user.value = result.data
  } else {
    error.value = result.error || 'Failed to load user'
    handleApiError(result)
  }

  loading.value = false
}

const handleToggleActive = async () => {
  if (!user.value) return

  toggling.value = true
  const result = await toggleActive(user.value.id)

  if (result.success && result.data) {
    user.value = result.data
    showSuccess(
      user.value.is_active 
        ? 'User activated successfully' 
        : 'User deactivated successfully'
    )
    confirmToggleActive.value = false
  } else {
    handleApiError(result, 'Failed to toggle user status')
  }

  toggling.value = false
}

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

onMounted(() => {
  loadUser()
})
</script>
