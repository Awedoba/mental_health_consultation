import type { Billing, ApiResponse } from '~/types'

export const useBillings = () => {
    const config = useRuntimeConfig()
    const { token } = useAuth()

    /**
     * List billings with optional filters
     */
    const list = async (filters?: {
        patient_id?: string
        consultation_id?: string
        payment_status?: 'pending' | 'partial' | 'paid' | 'waived'
        service_type?: 'consultation' | 'home_visit' | 'medication' | 'other'
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
            if (filters?.payment_status) params.append('payment_status', filters.payment_status)
            if (filters?.service_type) params.append('service_type', filters.service_type)
            if (filters?.date_from) params.append('date_from', filters.date_from)
            if (filters?.date_to) params.append('date_to', filters.date_to)
            if (filters?.search) params.append('search', filters.search)
            if (filters?.page) params.append('page', String(filters.page))
            if (filters?.per_page) params.append('per_page', String(filters.per_page))

            const query = params.toString()
            const url = `${config.public.apiBase}/billings${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<Billing[]>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data, meta: response.meta }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to fetch billings',
                data: [],
            }
        }
    }

    /**
     * Get single billing by ID
     */
    const show = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<Billing>>(
                `${config.public.apiBase}/billings/${id}`,
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
                error: error.data?.error?.message || 'Failed to fetch billing',
            }
        }
    }

    /**
     * Get billings for a specific patient
     */
    const getByPatient = async (patientId: string) => {
        try {
            const response = await $fetch<ApiResponse<Billing[]>>(
                `${config.public.apiBase}/billings/patient/${patientId}`,
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
                error: error.data?.error?.message || 'Failed to fetch patient billings',
                data: [],
            }
        }
    }

    /**
     * Create new billing
     */
    const create = async (billingData: {
        patient_id: string
        consultation_id?: string
        billing_date: string
        service_type: 'consultation' | 'home_visit' | 'medication' | 'other'
        amount: number
        nhis_covered?: boolean
        nhis_amount?: number
        patient_amount?: number
        payment_status?: 'pending' | 'partial' | 'paid' | 'waived'
        payment_date?: string
        payment_method?: 'cash' | 'mobile_money' | 'bank_transfer' | 'nhis'
        invoice_number?: string
        notes?: string
    }) => {
        try {
            const response = await $fetch<ApiResponse<Billing>>(
                `${config.public.apiBase}/billings`,
                {
                    method: 'POST',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: billingData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to create billing',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Update billing
     */
    const update = async (id: string, billingData: Partial<Billing>) => {
        try {
            const response = await $fetch<ApiResponse<Billing>>(
                `${config.public.apiBase}/billings/${id}`,
                {
                    method: 'PUT',
                    headers: {
                        Authorization: `Bearer ${token.value}`,
                    },
                    body: billingData,
                }
            )

            return { success: true, data: response.data, message: response.message }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to update billing',
                errors: error.data?.error?.errors,
            }
        }
    }

    /**
     * Delete billing
     */
    const remove = async (id: string) => {
        try {
            const response = await $fetch<ApiResponse<void>>(
                `${config.public.apiBase}/billings/${id}`,
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
                error: error.data?.error?.message || 'Failed to delete billing',
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

