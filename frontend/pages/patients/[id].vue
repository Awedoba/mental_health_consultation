<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div v-if="loading" class="text-center py-8">Loading...</div>
        <div v-else-if="error" class="text-red-600 py-4">{{ error }}</div>
        <div v-else-if="patient">
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
              {{ patient.last_name }}, {{ patient.first_name }}
            </h1>
            <NuxtLink
              :to="`/patients/${patient.id}/edit`"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
            >
              Edit
            </NuxtLink>
          </div>

          <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Demographics</h2>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <span class="text-sm text-gray-500">Date of Birth:</span>
                <p class="font-medium">
                  {{ new Date(patient.date_of_birth).toLocaleDateString() }}
                </p>
              </div>
              <div>
                <span class="text-sm text-gray-500">Gender:</span>
                <p class="font-medium">{{ patient.gender }}</p>
              </div>
              <div>
                <span class="text-sm text-gray-500">Phone:</span>
                <p class="font-medium">{{ patient.phone_number }}</p>
              </div>
              <div v-if="patient.email">
                <span class="text-sm text-gray-500">Email:</span>
                <p class="font-medium">{{ patient.email }}</p>
              </div>
              <div class="col-span-2">
                <span class="text-sm text-gray-500">Address:</span>
                <p class="font-medium">
                  {{ patient.address_line1 }}<br />
                  <span v-if="patient.address_line2">{{ patient.address_line2 }}<br /></span>
                  {{ patient.city }}, {{ patient.state_province }} {{ patient.postal_code }}
                </p>
              </div>
            </div>
          </div>

          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Emergency Contacts</h2>
            <div v-if="patient.emergency_contacts && patient.emergency_contacts.length > 0">
              <div
                v-for="contact in patient.emergency_contacts"
                :key="contact.id"
                class="border-b pb-4 mb-4 last:border-b-0"
              >
                <p class="font-medium">{{ contact.contact_name }}</p>
                <p class="text-sm text-gray-600">{{ contact.relationship }}</p>
                <p class="text-sm text-gray-600">{{ contact.phone_number }}</p>
              </div>
            </div>
            <p v-else class="text-gray-500">No emergency contacts</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth',
})

const route = useRoute()
const { show } = usePatients()

const patient = ref<Patient | null>(null)
const loading = ref(false)
const error = ref('')

onMounted(async () => {
  loading.value = true
  const result = await show(route.params.id as string)

  if (result.success) {
    patient.value = result.data
  } else {
    error.value = result.error || 'Failed to load patient'
  }

  loading.value = false
})
</script>

