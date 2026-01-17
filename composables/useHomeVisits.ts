import type { HomeVisit, ApiResponse } from '~/types'

export const useHomeVisits = () => {
    const config = useRuntimeConfig()
    const { token } = useAuth()

    /**
     * List home visits with optional filters
     */
    const list = async (filters?: {
        patient_id?: string
        clinician_id?: string
        date_from?: string
        date_to?: string
        search?: string
        page?: number
        per_page?: number
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.patient_id) params.append('patient_id', filters.patient_id)
            if (filters?.clinician_id) params.append('clinician_id', filters.clinician_id)
            if (filters?.date_from) params.append('date_from', filters.date_from)
            if (filters?.date_to) params.append('date_to', filters.date_to)
            if (filters?.search) params.append('search', filters.search)
            if (filters?.page) params.append('page', String(filters.page))
            if (filters?.per_page) params.append('per_page', String(filters.per_page))

            const query = params.toString()
            const url = `${config.public.apiBase}/home-visits${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<HomeVisit[]>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data, meta: response.meta }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to fetch home visits',
                data: [],
            }
        }
    }

    /**
     * Get single home visit by ID
     */
    const show = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<HomeVisit>>(
                `${config.public.apiBase}/home-visits/${id}`,
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
                error: error.data?.error?.message || 'Failed to fetch home visit',
            }
        }
    }

    /**
     * Get home visits for a specific patient
     */
    const getByPatient = async (patientId: string) => {
        try {
            const response = await $fetch<ApiResponse<HomeVisit[]>>(
                `${config.public.apiBase}/home-visits/patient/${patientId}`,
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
                error: error.data?.error?.message || 'Failed to fetch patient home visits',
                data: [],
            }
        }
    }

    /**
     * Create new home visit
     */
    const create = async (homeVisitData: {
        patient_id?: string
        client_name: string
        age?: number
        sex?: 'male' | 'female' | 'other'
        community_location: string
        contact: string
        visit_date: string
        visit_time?: string
        diagnosis_condition?: string
        medication_prescription?: string
        observations?: string
        impression?: string
        management?: string
        recommendation?: string
    }) => {
        try {
            const response = await $fetch<ApiResponse<HomeVisit>>(
                `${config.public.apiBase}/home-visits`,
                {
                    method: 'POST',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: homeVisitData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to create home visit',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Update home visit
     */
    const update = async (id: string, homeVisitData: Partial<HomeVisit>) => {
        try {
            const response = await $fetch<ApiResponse<HomeVisit>>(
                `${config.public.apiBase}/home-visits/${id}`,
                {
                    method: 'PUT',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: homeVisitData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to update home visit',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Delete home visit (admin only)
     */
    const remove = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<void>>(
                `${config.public.apiBase}/home-visits/${id}`,
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
                error: error.data?.error?.message || 'Failed to delete home visit',
            }
        }
    }

    return {
        list,
        show,
        getByPatient,
        create,
        update,
        remove,
    }
}

