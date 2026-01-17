import type { User, ApiResponse } from '~/types'

export const useUsers = () => {
    const config = useRuntimeConfig()
    const { token } = useAuth()

    /**
     * List all users (admin only)
     */
    const list = async (filters?: {
        search?: string
        role?: 'admin' | 'clinician'
        is_active?: boolean
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.search) params.append('search', filters.search)
            if (filters?.role) params.append('role', filters.role)
            if (filters?.is_active !== undefined) params.append('is_active', String(filters.is_active))

            const query = params.toString()
            const url = `${config.public.apiBase}/admin/users${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<User[]>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data, meta: response.meta }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to fetch users',
                data: [],
            }
        }
    }

    /**
     * Get single user by ID (admin only)
     */
    const show = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<User>>(
                `${config.public.apiBase}/admin/users/${id}`,
                {
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                }
            )

            return { success: true, data: response.data }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to fetch user',
            }
        }
    }

    /**
     * Create new user (admin only)
     */
    const create = async (userData: {
        username: string
        email: string
        first_name: string
        last_name: string
        role: 'admin' | 'clinician'
        password: string
        professional_title?: string
        license_number?: string
        phone?: string
    }) => {
        try {
            const response = await $fetch<ApiResponse<User>>(
                `${config.public.apiBase}/admin/users`,
                {
                    method: 'POST',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: userData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to create user',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Update user (admin only)
     */
    const update = async (
        id: string,
        userData: {
            email?: string
            first_name?: string
            last_name?: string
            role?: 'admin' | 'clinician'
            professional_title?: string
            license_number?: string
            phone?: string
        }
    ) => {
        try {
            const response = await $fetch<ApiResponse<User>>(
                `${config.public.apiBase}/admin/users/${id}`,
                {
                    method: 'PUT',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: userData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to update user',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Toggle user active status (admin only)
     */
    const toggleActive = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<User>>(
                `${config.public.apiBase}/admin/users/${id}/toggle-active`,
                {
                    method: 'POST',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to toggle user status',
            }
        }
    }

    /**
     * Delete user (admin only)
     */
    const remove = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<void>>(
                `${config.public.apiBase}/admin/users/${id}`,
                {
                    method: 'DELETE',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                }
            )

            return { success: true, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to delete user',
            }
        }
    }

    return {
        list,
        show,
        create,
        update,
        toggleActive,
        remove,
    }
}
