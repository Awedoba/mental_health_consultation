<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-900">Consultations</h1>
          <NuxtLink
            to="/consultations/create"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
          >
            New Consultation
          </NuxtLink>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
          <div v-if="loading" class="text-center py-8">Loading...</div>
          <div v-else-if="error" class="text-red-600 py-4">{{ error }}</div>
          <div v-else-if="consultations.length === 0" class="text-center py-8 text-gray-500">
            No consultations found
          </div>
          <div v-else class="space-y-4">
            <div
              v-for="consultation in consultations"
              :key="consultation.id"
              class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="font-semibold">
                    {{ consultation.patient?.first_name }} {{ consultation.patient?.last_name }}
                  </h3>
                  <p class="text-sm text-gray-600">
                    {{ new Date(consultation.consultation_date).toLocaleDateString() }} at
                    {{ consultation.consultation_time }}
                  </p>
                  <p class="text-sm text-gray-500">{{ consultation.session_type }}</p>
                </div>
                <NuxtLink
                  :to="`/consultations/${consultation.id}`"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  View
                </NuxtLink>
              </div>
            </div>
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

const config = useRuntimeConfig()
const { token } = useAuth()

const consultations = ref<any[]>([])
const loading = ref(false)
const error = ref('')

const loadConsultations = async () => {
  loading.value = true
  error.value = ''

  try {
    const { data } = await $fetch<{
      data: any[]
      meta: any
    }>(`${config.public.apiBase}/consultations`, {
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
    })

    consultations.value = data || []
  } catch (err: any) {
    error.value = err.data?.error?.message || 'Failed to load consultations'
  }

  loading.value = false
}

onMounted(() => {
  loadConsultations()
})
</script>

