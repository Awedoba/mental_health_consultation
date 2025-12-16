<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Edit Patient</h1>
      <NuxtLink
        :to="`/patients/${route.params.id}`"
        class="text-gray-600 hover:text-gray-900"
      >
        Cancel
      </NuxtLink>
    </div>

    <LoadingSpinner v-if="loading" size="lg" class="py-12">Loading patient...</LoadingSpinner>
    <FormError v-if="error" :error="error" />
    <form v-else-if="form" @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Demographics Section -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Demographics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
            <input
              v-model="form.first_name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
            <input
              v-model="form.middle_name"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
            <input
              v-model="form.last_name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth *</label>
            <input
              v-model="form.date_of_birth"
              type="date"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
            <select
              v-model="form.gender"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="">Select gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="non_binary">Non-binary</option>
              <option value="prefer_not_say">Prefer not to say</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
            <input
              v-model="form.phone_number"
              type="tel"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
            <select
              v-model="form.marital_status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="">Select status</option>
              <option value="single">Single</option>
              <option value="married">Married</option>
              <option value="divorced">Divorced</option>
              <option value="widowed">Widowed</option>
              <option value="separated">Separated</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
            <input
              v-model="form.occupation"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Education Level</label>
            <select
              v-model="form.education_level"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="">Select level</option>
              <option value="none">None</option>
              <option value="primary">Primary</option>
              <option value="secondary">Secondary</option>
              <option value="undergraduate">Undergraduate</option>
              <option value="graduate">Graduate</option>
              <option value="doctoral">Doctoral</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Address Section -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Address</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 *</label>
            <input
              v-model="form.address_line1"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
            <input
              v-model="form.address_line2"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
            <input
              v-model="form.city"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">State/Province *</label>
            <input
              v-model="form.state_province"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code *</label>
            <input
              v-model="form.postal_code"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
            <input
              v-model="form.country"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>
        </div>
      </div>

      <!-- Emergency Contacts Section -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">Emergency Contacts</h2>
          <button
            type="button"
            @click="addContact"
            class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1 rounded hover:bg-indigo-100"
          >
            + Add Contact
          </button>
        </div>

        <div v-if="form.emergency_contacts.length === 0" class="text-center py-4 text-gray-500 italic">
          No emergency contacts added.
        </div>

        <div v-else class="space-y-6">
          <div
            v-for="(contact, index) in form.emergency_contacts"
            :key="index"
            class="border rounded-lg p-4 relative"
          >
            <button
              type="button"
              @click="removeContact(index)"
              class="absolute top-2 right-2 text-red-600 hover:text-red-800"
            >
              Remove
            </button>

            <h3 class="text-sm font-medium text-gray-900 mb-3">Contact {{ index + 1 }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input
                  v-model="contact.contact_name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Relationship *</label>
                <select
                  v-model="contact.relationship"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                  <option value="">Select relationship</option>
                  <option value="spouse">Spouse</option>
                  <option value="parent">Parent</option>
                  <option value="child">Child</option>
                  <option value="sibling">Sibling</option>
                  <option value="friend">Friend</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                <input
                  v-model="contact.phone_number"
                  type="tel"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input
                  v-model="contact.email"
                  type="email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>

               <div class="md:col-span-2 flex items-center">
                <input
                  :id="`primary-${index}`"
                  v-model="contact.is_primary"
                  type="checkbox"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label :for="`primary-${index}`" class="ml-2 block text-sm text-gray-900">
                  Primary Contact
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-4">
        <NuxtLink
          :to="`/patients/${route.params.id}`"
          class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </NuxtLink>
        <button
          type="submit"
          :disabled="saving"
          class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
        >
          {{ saving ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>

      <div v-if="saveError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
        {{ saveError }}
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth',
  layout: 'default',
})

const route = useRoute()
const router = useRouter()
const { show, update } = usePatients()

const form = ref<any>(null)
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const saveError = ref('')

// Load patient data
onMounted(async () => {
  loading.value = true
  const result = await show(route.params.id as string)

  if (result.success && result.data) {
    // Format date for input (YYYY-MM-DD)
    const formatDate = (dateString: string | null | undefined): string => {
      if (!dateString) return ''
      // If already in YYYY-MM-DD format, return as is
      if (typeof dateString === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        return dateString
      }
      // Otherwise parse and format
      const date = new Date(dateString)
      if (isNaN(date.getTime())) return ''
      return date.toISOString().split('T')[0]
    }

    // Populate form with existing data
    form.value = {
      first_name: result.data.first_name || '',
      middle_name: result.data.middle_name || '',
      last_name: result.data.last_name || '',
      date_of_birth: formatDate(result.data.date_of_birth),
      gender: result.data.gender || '',
      phone_number: result.data.phone_number || '',
      email: result.data.email || '',
      address_line1: result.data.address_line1 || '',
      address_line2: result.data.address_line2 || '',
      city: result.data.city || '',
      state_province: result.data.state_province || '',
      postal_code: result.data.postal_code || '',
      country: result.data.country || '',
      marital_status: result.data.marital_status || '',
      occupation: result.data.occupation || '',
      education_level: result.data.education_level || '',
      emergency_contacts: result.data.emergency_contacts ? result.data.emergency_contacts.map((c: any) => ({
        id: c.id,
        contact_name: c.contact_name || '',
        relationship: c.relationship || '',
        phone_number: c.phone_number || '',
        email: c.email || '',
        is_primary: c.is_primary || false,
      })) : [],
    }
  } else {
    error.value = result.error || 'Failed to load patient'
  }

  loading.value = false
})

const addContact = () => {
  if (!form.value.emergency_contacts) {
    form.value.emergency_contacts = []
  }
  form.value.emergency_contacts.push({
    contact_name: '',
    relationship: '',
    phone_number: '',
    email: '',
    is_primary: false,
  })
}

const removeContact = (index: number) => {
  form.value.emergency_contacts.splice(index, 1)
}

// Handle form submission
const handleSubmit = async () => {
  saving.value = true
  saveError.value = ''

  const result = await update(route.params.id as string, form.value)

  if (result.success) {
    // Redirect back to patient detail page
    router.push(`/patients/${route.params.id}`)
  } else {
    saveError.value = result.error || 'Failed to update patient'
  }

  saving.value = false
}
</script>
