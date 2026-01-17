import type { Prescription, ApiResponse } from '~/types'

export const usePrescriptions = () => {
    const config = useRuntimeConfig()
    const { token } = useAuth()

    /**
     * List prescriptions with optional filters
     */
    const list = async (filters?: {
        patient_id?: string
        consultation_id?: string
        is_active?: boolean
        date_from?: string
        date_to?: string
        search?: string
        page?: number
        per_page?: number
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.patient_id) params.append('patient_id', filters.patient_id)
            if (filters?.consultation_id) params.append('consultation_id', filters.consultation_id)
            if (filters?.is_active !== undefined) params.append('is_active', String(filters.is_active))
            if (filters?.date_from) params.append('date_from', filters.date_from)
            if (filters?.date_to) params.append('date_to', filters.date_to)
            if (filters?.search) params.append('search', filters.search)
            if (filters?.page) params.append('page', String(filters.page))
            if (filters?.per_page) params.append('per_page', String(filters.per_page))

            const query = params.toString()
            const url = `${config.public.apiBase}/prescriptions${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<Prescription[]>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data, meta: response.meta }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to fetch prescriptions',
                data: [],
            }
        }
    }

    /**
     * Get single prescription by ID
     */
    const show = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<Prescription>>(
                `${config.public.apiBase}/prescriptions/${id}`,
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
                error: error.data?.error?.message || 'Failed to fetch prescription',
            }
        }
    }

    /**
     * Get prescriptions for a specific patient
     */
    const getByPatient = async (patientId: string, isActive?: boolean) => {
        try {
            const params = new URLSearchParams()
            if (isActive !== undefined) params.append('is_active', String(isActive))

            const query = params.toString()
            const url = `${config.public.apiBase}/prescriptions/patient/${patientId}${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<Prescription[]>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to fetch patient prescriptions',
                data: [],
            }
        }
    }

    /**
     * Create new prescription
     */
    const create = async (prescriptionData: {
        patient_id: string
        consultation_id?: string
        prescription_date: string
        medication_id: string
        dosage: string
        frequency: string
        duration: string
        quantity?: number
        instructions?: string
        refills?: number
        is_active?: boolean
        start_date?: string
        end_date?: string
    }) => {
        try {
            const response = await $fetch<ApiResponse<Prescription>>(
                `${config.public.apiBase}/prescriptions`,
                {
                    method: 'POST',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: prescriptionData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to create prescription',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Update prescription
     */
    const update = async (id: string, prescriptionData: Partial<Prescription>) => {
        try {
            const response = await $fetch<ApiResponse<Prescription>>(
                `${config.public.apiBase}/prescriptions/${id}`,
                {
                    method: 'PUT',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: prescriptionData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to update prescription',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Delete prescription (admin only)
     */
    const remove = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<void>>(
                `${config.public.apiBase}/prescriptions/${id}`,
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
                error: error.data?.error?.message || 'Failed to delete prescription',
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

