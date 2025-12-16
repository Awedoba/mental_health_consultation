<template>
  <div>
    <div class="mb-6">
      <NuxtLink
        :to="`/admin/users/${route.params.id}`"
        class="text-indigo-600 hover:text-indigo-900 flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to User
      </NuxtLink>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit User</h1>

    <LoadingSpinner v-if="initialLoading" size="lg" class="py-12">Loading user...</LoadingSpinner>

    <div v-else class="space-y-6">
      <div class="bg-white shadow rounded-lg p-6">
        <form @submit.prevent="handleSubmit">
          <FormError :error="error" :errors="errors" />

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Username (Read-only) -->
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700">
                Username (Cannot be changed)
              </label>
              <input
                :value="form.username"
                type="text"
                disabled
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-500 cursor-not-allowed"
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-gray-700">
                Email *
              </label>
              <input
                v-model="form.email"
                type="email"
                required
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <!-- Phone -->
            <div>
              <label class="block text-sm font-medium text-gray-700">
                Phone
              </label>
              <input
                v-model="form.phone"
                type="tel"
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <!-- First Name -->
            <div>
              <label class="block text-sm font-medium text-gray-700">
                First Name *
              </label>
              <input
                v-model="form.first_name"
                type="text"
                required
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <!-- Last Name -->
            <div>
              <label class="block text-sm font-medium text-gray-700">
                Last Name *
              </label>
              <input
                v-model="form.last_name"
                type="text"
                required
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <!-- Role -->
            <div>
              <label class="block text-sm font-medium text-gray-700">
                Role *
              </label>
              <select
                v-model="form.role"
                required
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="clinician">Clinician</option>
                <option value="admin">Administrator</option>
              </select>
            </div>

            <!-- Professional Title -->
            <div>
              <label class="block text-sm font-medium text-gray-700">
                Professional Title
              </label>
              <input
                v-model="form.professional_title"
                type="text"
                placeholder="MD, PhD, LCSW, etc."
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <!-- License Number -->
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700">
                License Number
              </label>
              <input
                v-model="form.license_number"
                type="text"
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="mt-6 flex justify-end gap-3">
            <NuxtLink
              :to="`/admin/users/${route.params.id}`"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </NuxtLink>
            <button
              type="submit"
              :disabled="submitting"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <LoadingSpinner v-if="submitting" size="sm" color="white" class="mr-2" />
              {{ submitting ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Password Change Section -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h2>
        <p class="text-sm text-gray-600 mb-4">
          Leave blank to keep the current password. Password must be at least 12 characters and include uppercase, lowercase, number, and special character.
        </p>
        
        <form @submit.prevent="handlePasswordChange">
          <FormError :error="passwordError" />

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700">
                New Password
              </label>
              <input
                v-model="passwordForm.new_password"
                type="password"
                minlength="12"
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700">
                Confirm New Password
              </label>
              <input
                v-model="passwordForm.confirm_password"
                type="password"
                minlength="12"
                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button
              type="submit"
              :disabled="changingPassword || !passwordForm.new_password"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <LoadingSpinner v-if="changingPassword" size="sm" color="white" class="mr-2" />
              {{ changingPassword ? 'Changing...' : 'Change Password' }}
            </button>
          </div>
        </form>
      </div>
    </div>
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
const { show, update } = useUsers()
const { handleApiError, showSuccess, getValidationErrors } = useErrorHandler()
const config = useRuntimeConfig()
const { token } = useAuth()

const initialLoading = ref(true)
const submitting = ref(false)
const error = ref<string | null>(null)
const errors = ref<Record<string, string[]>>({})

const form = reactive({
  username: '',
  email: '',
  first_name: '',
  last_name: '',
  role: 'clinician' as 'admin' | 'clinician',
  professional_title: '',
  license_number: '',
  phone: '',
})

// Password change
const changingPassword = ref(false)
const passwordError = ref<string | null>(null)
const passwordForm = reactive({
  new_password: '',
  confirm_password: '',
})

const loadUser = async () => {
  initialLoading.value = true
  const result = await show(route.params.id as string)

  if (result.success && result.data) {
    const user = result.data
    form.username = user.username
    form.email = user.email
    form.first_name = user.first_name
    form.last_name = user.last_name
    form.role = user.role
    form.professional_title = user.professional_title || ''
    form.license_number = user.license_number || ''
    form.phone = user.phone || ''
  } else {
    error.value = result.error || 'Failed to load user'
    handleApiError(result)
  }

  initialLoading.value = false
}

const handleSubmit = async () => {
  submitting.value = true
  error.value = null
  errors.value = {}

  const result = await update(route.params.id as string, {
    email: form.email,
    first_name: form.first_name,
    last_name: form.last_name,
    role: form.role,
    professional_title: form.professional_title || undefined,
    license_number: form.license_number || undefined,
    phone: form.phone || undefined,
  })

  if (result.success) {
    showSuccess('User updated successfully')
    router.push(`/admin/users/${route.params.id}`)
  } else {
    error.value = result.error || 'Failed to update user'
    errors.value = result.errors || {}
    handleApiError(result, 'Failed to update user')
  }

  submitting.value = false
}

const handlePasswordChange = async () => {
  if (passwordForm.new_password !== passwordForm.confirm_password) {
    passwordError.value = 'Passwords do not match'
    return
  }

  changingPassword.value = true
  passwordError.value = null

  try {
    await $fetch(`${config.public.apiBase}/admin/users/${route.params.id}/password`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
      body: {
        new_password: passwordForm.new_password,
      },
    })

    showSuccess('Password changed successfully')
    passwordForm.new_password = ''
    passwordForm.confirm_password = ''
  } catch (err: any) {
    passwordError.value = err.data?.error?.message || 'Failed to change password'
    handleApiError(err, 'Failed to change password')
  }

  changingPassword.value = false
}

onMounted(() => {
  loadUser()
})
</script>
