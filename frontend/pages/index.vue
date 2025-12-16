<script setup lang="ts">
const { token } = useAuth()

// Redirect based on token existence
// The auth plugin will handle fetching user data
if (token.value) {
  await navigateTo('/dashboard', { replace: true })
} else {
  await navigateTo('/login', { replace: true })
}
</script>

<template>
  <div></div>
</template>

