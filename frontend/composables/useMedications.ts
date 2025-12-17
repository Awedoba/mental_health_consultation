import type { Medication, ApiResponse } from '~/types'

export const useMedications = () => {
    const config = useRuntimeConfig()
    const { token } = useAuth()

    /**
     * List medications with optional search
     */
    const list = async (filters?: {
        search?: string
        category?: string
        dosage_form?: 'tablet' | 'capsule' | 'syrup' | 'injection' | 'other'
        include_inactive?: boolean
        per_page?: number
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.search) params.append('search', filters.search)
            if (filters?.category) params.append('category', filters.category)
            if (filters?.dosage_form) params.append('dosage_form', filters.dosage_form)
            if (filters?.include_inactive) params.append('include_inactive', 'true')
            if (filters?.per_page) params.append('per_page', String(filters.per_page))

            const query = params.toString()
            const url = `${config.public.apiBase}/medications${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<Medication[]>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data, meta: response.meta }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to fetch medications',
                data: [],
            }
        }
    }

    /**
     * Get single medication by ID
     */
    const show = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<Medication>>(
                `${config.public.apiBase}/medications/${id}`,
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
                error: error.data?.error?.message || 'Failed to fetch medication',
            }
        }
    }

    /**
     * Create new medication (admin only)
     */
    const create = async (medicationData: {
        name: string
        generic_name?: string
        dosage_form?: 'tablet' | 'capsule' | 'syrup' | 'injection' | 'other'
        strength?: string
        category?: string
        is_active?: boolean
    }) => {
        try {
            const response = await $fetch<ApiResponse<Medication>>(
                `${config.public.apiBase}/medications`,
                {
                    method: 'POST',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: medicationData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to create medication',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Update medication (admin only)
     */
    const update = async (id: string, medicationData: Partial<Medication>) => {
        try {
            const response = await $fetch<ApiResponse<Medication>>(
                `${config.public.apiBase}/medications/${id}`,
                {
                    method: 'PUT',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: medicationData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to update medication',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Delete medication (admin only)
     */
    const remove = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<void>>(
                `${config.public.apiBase}/medications/${id}`,
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
                error: error.data?.error?.message || 'Failed to delete medication',
            }
        }
    }

    return {
        list,
        show,
        create,
        update,
        remove,
    }
}

