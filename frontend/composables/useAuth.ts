import type { User } from '~/types'

export const useAuth = () => {
  const token = useCookie<string | null>('auth_token', {
    secure: true,
    sameSite: 'strict',
    httpOnly: false, // Required for client-side access
  })
  
  const user = useState<User | null>('user', () => null)
  const isAuthenticated = computed(() => !!token.value && !!user.value)

  const login = async (username: string, password: string) => {
    try {
      const config = useRuntimeConfig()
      const { data } = await $fetch<{
        data: {
          user: User
          token: string
        }
        message: string
      }>(`${config.public.apiBase}/auth/login`, {
        method: 'POST',
        body: {
          username,
          password,
        },
      })

      token.value = data.token
      user.value = data.user

      return { success: true, data }
    } catch (error: any) {
      return {
        success: false,
        error: error.data?.error?.message || 'Login failed',
      }
    }
  }

  const logout = async () => {
    try {
      const config = useRuntimeConfig()
      await $fetch(`${config.public.apiBase}/auth/logout`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      })
    } catch (error) {
      // Continue with logout even if API call fails
    } finally {
      token.value = null
      user.value = null
      navigateTo('/login')
    }
  }

  const fetchUser = async () => {
    try {
      const config = useRuntimeConfig()
      const { data } = await $fetch<{
        data: User
      }>(`${config.public.apiBase}/auth/me`, {
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      })

      user.value = data
      return { success: true, data }
    } catch (error) {
      token.value = null
      user.value = null
      return { success: false }
    }
  }

  const changePassword = async (currentPassword: string, newPassword: string) => {
    try {
      const config = useRuntimeConfig()
      const { message } = await $fetch<{
        message: string
      }>(`${config.public.apiBase}/auth/password/change`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
        body: {
          current_password: currentPassword,
          new_password: newPassword,
        },
      })

      return { success: true, message }
    } catch (error: any) {
      return {
        success: false,
        error: error.data?.error?.message || 'Password change failed',
      }
    }
  }

  return {
    token: readonly(token),
    user: readonly(user),
    isAuthenticated,
    login,
    logout,
    fetchUser,
    changePassword,
  }
}

