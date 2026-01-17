export default defineNuxtRouteMiddleware(async (to, from) => {
  const { token, user, fetchUser } = useAuth()

  // If no token, redirect to login
  if (!token.value) {
    return navigateTo('/login')
  }

  // If we have a token but no user data, fetch it
  if (!user.value) {
    const result = await fetchUser()

    // If fetch failed (invalid/expired token), redirect to login
    if (!result.success) {
      return navigateTo('/login')
    }
  }
})

