<template>
  <div class="relative">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <!-- Search Input -->
    <div class="relative">
      <input
        v-model="searchQuery"
        type="text"
        :placeholder="placeholder"
        :required="required"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
        @input="handleSearch"
        @focus="showDropdown = true"
        @blur="handleBlur"
      />
      <div v-if="loading" class="absolute right-3 top-2.5">
        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </div>

    <!-- Selected Medication Display -->
    <div v-if="selectedMedication" class="mt-2 p-3 bg-indigo-50 border border-indigo-200 rounded-md">
      <div class="flex justify-between items-start">
        <div>
          <p class="font-medium text-indigo-900">{{ selectedMedication.name }}</p>
          <p v-if="selectedMedication.generic_name" class="text-sm text-indigo-700">
            Generic: {{ selectedMedication.generic_name }}
          </p>
          <p v-if="selectedMedication.strength" class="text-sm text-indigo-600">
            {{ selectedMedication.strength }}
            <span v-if="selectedMedication.dosage_form">
              ({{ selectedMedication.dosage_form }})
            </span>
          </p>
        </div>
        <button
          type="button"
          @click="clearSelection"
          class="text-indigo-600 hover:text-indigo-800 text-sm"
        >
          Clear
        </button>
      </div>
    </div>

    <!-- Dropdown Results -->
    <div
      v-if="showDropdown && (medications.length > 0 || searchQuery)"
      class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
    >
      <div v-if="medications.length === 0 && !loading" class="p-4 text-sm text-gray-500 text-center">
        No medications found
      </div>
      <div
        v-for="medication in medications"
        :key="medication.id"
        @mousedown="selectMedication(medication)"
        class="p-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0"
      >
        <p class="font-medium text-gray-900">{{ medication.name }}</p>
        <p v-if="medication.generic_name" class="text-sm text-gray-600">
          Generic: {{ medication.generic_name }}
        </p>
        <p v-if="medication.strength" class="text-sm text-gray-500">
          {{ medication.strength }}
          <span v-if="medication.dosage_form">
            ({{ medication.dosage_form }})
          </span>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Medication } from '~/types'
import { useDebounceFn } from '@vueuse/core'

interface Props {
  modelValue?: string // medication_id
  label?: string
  placeholder?: string
  required?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Search for medication...',
  required: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: string | undefined]
  'medication-selected': [medication: Medication]
}>()

const { list } = useMedications()

const searchQuery = ref('')
const medications = ref<Medication[]>([])
const selectedMedication = ref<Medication | null>(null)
const showDropdown = ref(false)
const loading = ref(false)

const handleSearch = useDebounceFn(async () => {
  if (!searchQuery.value || searchQuery.value.length < 2) {
    medications.value = []
    return
  }

  loading.value = true
  const result = await list({ search: searchQuery.value, per_page: 20 })
  
  if (result.success) {
    medications.value = result.data
  }
  
  loading.value = false
}, 300)

const selectMedication = (medication: Medication) => {
  selectedMedication.value = medication
  searchQuery.value = medication.name
  emit('update:modelValue', medication.id)
  emit('medication-selected', medication)
  showDropdown.value = false
}

const clearSelection = () => {
  selectedMedication.value = null
  searchQuery.value = ''
  emit('update:modelValue', undefined)
  showDropdown.value = false
}

const handleBlur = () => {
  // Delay to allow click events on dropdown items
  setTimeout(() => {
    showDropdown.value = false
  }, 200)
}

// Load initial medications if no search query
onMounted(async () => {
  if (!searchQuery.value) {
    const result = await list({ per_page: 10 })
    if (result.success) {
      medications.value = result.data
    }
  }
})
</script>

