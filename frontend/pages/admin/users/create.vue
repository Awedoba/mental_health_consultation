<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Create New User</h1>

        <div class="bg-white shadow rounded-lg p-6">
          <form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Username *</label>
                <input
                  v-model="form.username"
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email *</label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">First Name *</label>
                <input
                  v-model="form.first_name"
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Last Name *</label>
                <input
                  v-model="form.last_name"
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Role *</label>
                <select
                  v-model="form.role"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                >
                  <option value="clinician">Clinician</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Password *</label>
                <input
                  v-model="form.password"
                  type="password"
                  required
                  minlength="12"
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
                <p class="mt-1 text-xs text-gray-500">
                  Min 12 chars, must include uppercase, lowercase, number, and special character
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Professional Title</label>
                <input
                  v-model="form.professional_title"
                  type="text"
                  placeholder="MD, PhD, LCSW, etc."
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">License Number</label>
                <input
                  v-model="form.license_number"
                  type="text"
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
            </div>

            <FormError :error="error" :errors="validationErrors" />

            <div class="mt-6 flex justify-end space-x-4">
              <NuxtLink
                to="/admin/users"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </NuxtLink>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
              >
                <span v-if="loading">Creating...</span>
                <span v-else>Create User</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['auth', 'role'],
})

const { create } = useUsers()
const { showSuccess } = useErrorHandler()
const router = useRouter()

const form = reactive({
  username: '',
  email: '',
  first_name: '',
  last_name: '',
  role: 'clinician' as 'admin' | 'clinician',
  password: '',
  professional_title: '',
  license_number: '',
})

const loading = ref(false)
const error = ref('')
const validationErrors = ref<Record<string, string[]> | null>(null)

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  validationErrors.value = null

  const result = await create(form)

  if (result.success) {
    showSuccess('User created successfully')
    router.push('/admin/users')
  } else {
    error.value = result.error || 'Failed to create user'
    validationErrors.value = result.errors || null
  }

  loading.value = false
}
</script>

