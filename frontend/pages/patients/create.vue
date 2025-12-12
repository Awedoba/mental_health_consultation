<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Create New Patient</h1>

        <div class="bg-white shadow rounded-lg p-6">
          <form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-2 gap-6">
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
                <label class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                <input
                  v-model="form.date_of_birth"
                  type="date"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Gender *</label>
                <select
                  v-model="form.gender"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                >
                  <option value="">Select...</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="non_binary">Non-binary</option>
                  <option value="prefer_not_say">Prefer not to say</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Phone Number *</label>
                <input
                  v-model="form.phone_number"
                  type="tel"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Address Line 1 *</label>
                <input
                  v-model="form.address_line1"
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Address Line 2</label>
                <input
                  v-model="form.address_line2"
                  type="text"
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">City *</label>
                <input
                  v-model="form.city"
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">State/Province *</label>
                <input
                  v-model="form.state_province"
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Postal Code *</label>
                <input
                  v-model="form.postal_code"
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Country *</label>
                <input
                  v-model="form.country"
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                />
              </div>
            </div>

            <div class="mt-6">
              <h3 class="text-lg font-medium mb-4">Emergency Contact *</h3>
              <div class="grid grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Contact Name *</label>
                  <input
                    v-model="form.emergency_contacts[0].contact_name"
                    type="text"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Relationship *</label>
                  <select
                    v-model="form.emergency_contacts[0].relationship"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                  >
                    <option value="">Select...</option>
                    <option value="spouse">Spouse</option>
                    <option value="parent">Parent</option>
                    <option value="child">Child</option>
                    <option value="sibling">Sibling</option>
                    <option value="friend">Friend</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Phone Number *</label>
                  <input
                    v-model="form.emergency_contacts[0].phone_number"
                    type="tel"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                  />
                </div>
                <div>
                  <label class="flex items-center">
                    <input
                      v-model="form.emergency_contacts[0].is_primary"
                      type="checkbox"
                      class="mr-2"
                    />
                    <span class="text-sm text-gray-700">Primary Contact</span>
                  </label>
                </div>
              </div>
            </div>

            <div v-if="error" class="mt-4 text-red-600">{{ error }}</div>

            <div class="mt-6 flex justify-end space-x-4">
              <NuxtLink
                to="/patients"
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
                <span v-else>Create Patient</span>
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
  middleware: 'auth',
})

const { create } = usePatients()
const router = useRouter()

const form = reactive({
  first_name: '',
  last_name: '',
  date_of_birth: '',
  gender: '',
  phone_number: '',
  email: '',
  address_line1: '',
  address_line2: '',
  city: '',
  state_province: '',
  postal_code: '',
  country: 'USA',
  emergency_contacts: [
    {
      contact_name: '',
      relationship: '',
      phone_number: '',
      is_primary: true,
    },
  ],
})

const loading = ref(false)
const error = ref('')

const handleSubmit = async () => {
  loading.value = true
  error.value = ''

  const result = await create(form)

  if (result.success) {
    router.push(`/patients/${result.data.id}`)
  } else {
    error.value = result.error || 'Failed to create patient'
    if (result.errors) {
      console.error('Validation errors:', result.errors)
    }
  }

  loading.value = false
}
</script>

