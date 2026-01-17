export default defineNuxtPlugin(async () => {
    const { token, fetchUser } = useAuth()

    // If we have a token on app initialization, fetch the user data
    if (token.value) {
        await fetchUser()
    }
})
