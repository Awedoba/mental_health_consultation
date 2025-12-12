<script setup lang="ts">
const { isAuthenticated } = useAuth()

// Redirect based on authentication
if (isAuthenticated.value) {
  await navigateTo('/dashboard', { replace: true })
} else {
  await navigateTo('/login', { replace: true })
}
</script>

<template>
  <div></div>
</template>

