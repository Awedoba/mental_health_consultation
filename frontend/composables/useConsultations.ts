import type { Consultation, ApiResponse } from '~/types'

export const useConsultations = () => {
    const config = useRuntimeConfig()
    const { token } = useAuth()

    /**
     * List consultations with optional filters
     */
    const list = async (filters?: {
        patient_id?: string
        clinician_id?: string
        status?: 'locked' | 'unlocked'
        risk_assessment?: 'low' | 'moderate' | 'high'
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
            if (filters?.status) params.append('status', filters.status)
            if (filters?.risk_assessment) params.append('risk_assessment', filters.risk_assessment)
            if (filters?.date_from) params.append('date_from', filters.date_from)
            if (filters?.date_to) params.append('date_to', filters.date_to)
            if (filters?.search) params.append('search', filters.search)
            if (filters?.page) params.append('page', String(filters.page))
            if (filters?.per_page) params.append('per_page', String(filters.per_page))

            const query = params.toString()
            const url = `${config.public.apiBase}/consultations${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<Consultation[]>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data, meta: response.meta }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to fetch consultations',
                data: [],
            }
        }
    }

    /**
     * Get single consultation by ID
     */
    const show = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<Consultation>>(
                `${config.public.apiBase}/consultations/${id}`,
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
                error: error.data?.error?.message || 'Failed to fetch consultation',
            }
        }
    }

    /**
     * Create new consultation
     */
    const create = async (consultationData: {
        patient_id: string
        primary_clinician_id: string
        consultation_date: string
        consultation_time: string
        session_type: string
        session_duration?: number
        chief_complaint: string
        history_present_illness: string
        risk_assessment: 'low' | 'moderate' | 'high'
        [key: string]: any
    }) => {
        try {
            const response = await $fetch<ApiResponse<Consultation>>(
                `${config.public.apiBase}/consultations`,
                {
                    method: 'POST',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: consultationData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to create consultation',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Update consultation
     */
    const update = async (id: string, consultationData: Partial<Consultation>) => {
        try {
            const response = await $fetch<ApiResponse<Consultation>>(
                `${config.public.apiBase}/consultations/${id}`,
                {
                    method: 'PUT',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: consultationData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to update consultation',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Lock consultation (finalize)
     */
    const lock = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<Consultation>>(
                `${config.public.apiBase}/consultations/${id}/lock`,
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
                error: error.data?.error?.message || 'Failed to lock consultation',
            }
        }
    }

    /**
     * Delete consultation
     */
    const remove = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<void>>(
                `${config.public.apiBase}/consultations/${id}`,
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
                error: error.data?.error?.message || 'Failed to delete consultation',
            }
        }
    }

    return {
        list,
        show,
        create,
        update,
        lock,
        remove,
    }
}
