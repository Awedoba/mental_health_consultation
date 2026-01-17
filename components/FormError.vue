<template>
  <div v-if="error || errors" class="rounded-md bg-red-50 p-4 mb-4">
    <div class="flex">
      <div class="flex-shrink-0">
        <svg
          class="h-5 w-5 text-red-400"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
      <div class="ml-3 flex-1">
        <!-- Single error message -->
        <p v-if="error && !errors" class="text-sm font-medium text-red-800">
          {{ error }}
        </p>

        <!-- Multiple validation errors -->
        <div v-if="errors && Object.keys(errors).length > 0">
          <h3 class="text-sm font-medium text-red-800">
            {{ title || 'Please correct the following errors:' }}
          </h3>
          <div class="mt-2 text-sm text-red-700">
            <ul role="list" class="list-disc pl-5 space-y-1">
              <li v-for="(fieldErrors, field) in errors" :key="field">
                <span class="font-medium">{{ formatFieldName(field) }}:</span>
                {{ Array.isArray(fieldErrors) ? fieldErrors[0] : fieldErrors }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  error?: string | null
  errors?: Record<string, string | string[]> | null
  title?: string
}

const props = defineProps<Props>()

// Format field name from snake_case to Title Case
const formatFieldName = (field: string): string => {
  return field
    .split('_')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}
</script>
