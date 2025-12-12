<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div v-if="loading" class="text-center py-8">Loading...</div>
        <div v-else-if="error" class="text-red-600 py-4">{{ error }}</div>
        <div v-else-if="consultation">
          <h1 class="text-3xl font-bold text-gray-900 mb-6">Consultation Details</h1>

          <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Patient Information</h2>
            <p class="font-medium">
              {{ consultation.patient?.first_name }} {{ consultation.patient?.last_name }}
            </p>
            <p class="text-sm text-gray-600">
              {{ new Date(consultation.consultation_date).toLocaleDateString() }} at
              {{ consultation.consultation_time }}
            </p>
          </div>

          <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Chief Complaint</h2>
            <p>{{ consultation.chief_complaint }}</p>
          </div>

          <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">History of Present Illness</h2>
            <p class="whitespace-pre-wrap">{{ consultation.history_present_illness }}</p>
          </div>

          <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Clinical Summary</h2>
            <p class="whitespace-pre-wrap">{{ consultation.clinical_summary }}</p>
          </div>

          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Risk Assessment</h2>
            <span
              :class="{
                'bg-green-100 text-green-800': consultation.risk_assessment === 'low',
                'bg-yellow-100 text-yellow-800': consultation.risk_assessment === 'moderate',
                'bg-red-100 text-red-800': consultation.risk_assessment === 'high',
              }"
              class="px-3 py-1 rounded-full text-sm font-semibold"
            >
              {{ consultation.risk_assessment }}
            </span>
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
const config = useRuntimeConfig()
const { token } = useAuth()

const consultation = ref<any>(null)
const loading = ref(false)
const error = ref('')

onMounted(async () => {
  loading.value = true

  try {
    const { data } = await $fetch<{
      data: any
    }>(`${config.public.apiBase}/consultations/${route.params.id}`, {
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
    })

    consultation.value = data
  } catch (err: any) {
    error.value = err.data?.error?.message || 'Failed to load consultation'
  }

  loading.value = false
})
</script>

