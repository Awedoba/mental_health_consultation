import type { ApiResponse } from '~/types'

export const useReports = () => {
    const config = useRuntimeConfig()
    const { token } = useAuth()

    /**
     * Client List Report
     */
    const clientList = async (filters?: {
        name?: string
        age_from?: number
        age_to?: number
        sex?: string
        nhis_status?: string
        date_from?: string
        date_to?: string
        is_active?: boolean
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.name) params.append('name', filters.name)
            if (filters?.age_from) params.append('age_from', String(filters.age_from))
            if (filters?.age_to) params.append('age_to', String(filters.age_to))
            if (filters?.sex) params.append('sex', filters.sex)
            if (filters?.nhis_status) params.append('nhis_status', filters.nhis_status)
            if (filters?.date_from) params.append('date_from', filters.date_from)
            if (filters?.date_to) params.append('date_to', filters.date_to)
            if (filters?.is_active !== undefined) params.append('is_active', String(filters.is_active))

            const query = params.toString()
            const url = `${config.public.apiBase}/reports/client-list${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<any>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to generate client list report',
                data: [],
            }
        }
    }

    /**
     * Client Query Report (Advanced search)
     */
    const clientQuery = async (filters?: {
        search?: string
        age_from?: number
        age_to?: number
        sex?: string
        marital_status?: string
        nhis_status?: string
        city?: string
        town?: string
        created_from?: string
        created_to?: string
        is_active?: boolean
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.search) params.append('search', filters.search)
            if (filters?.age_from) params.append('age_from', String(filters.age_from))
            if (filters?.age_to) params.append('age_to', String(filters.age_to))
            if (filters?.sex) params.append('sex', filters.sex)
            if (filters?.marital_status) params.append('marital_status', filters.marital_status)
            if (filters?.nhis_status) params.append('nhis_status', filters.nhis_status)
            if (filters?.city) params.append('city', filters.city)
            if (filters?.town) params.append('town', filters.town)
            if (filters?.created_from) params.append('created_from', filters.created_from)
            if (filters?.created_to) params.append('created_to', filters.created_to)
            if (filters?.is_active !== undefined) params.append('is_active', String(filters.is_active))

            const query = params.toString()
            const url = `${config.public.apiBase}/reports/client-query${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<any>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to generate client query report',
                data: [],
            }
        }
    }

    /**
     * Consulting History Report
     */
    const consultingHistory = async (patientId: string, filters?: {
        date_from?: string
        date_to?: string
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.date_from) params.append('date_from', filters.date_from)
            if (filters?.date_to) params.append('date_to', filters.date_to)

            const query = params.toString()
            const url = `${config.public.apiBase}/reports/consulting-history/${patientId}${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<any>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to generate consulting history report',
            }
        }
    }

    /**
     * Next Visits Report
     */
    const nextVisits = async (filters?: {
        date_from?: string
        date_to?: string
        clinician_id?: string
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.date_from) params.append('date_from', filters.date_from)
            if (filters?.date_to) params.append('date_to', filters.date_to)
            if (filters?.clinician_id) params.append('clinician_id', filters.clinician_id)

            const query = params.toString()
            const url = `${config.public.apiBase}/reports/next-visits${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<any>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to generate next visits report',
                data: [],
            }
        }
    }

    /**
     * Consulting Data Query Report
     */
    const consultingDataQuery = async (filters?: {
        date_from?: string
        date_to?: string
        session_type?: string
        patient_id?: string
        clinician_id?: string
        has_mse?: boolean
        has_diagnosis?: boolean
        has_management_plan?: boolean
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.date_from) params.append('date_from', filters.date_from)
            if (filters?.date_to) params.append('date_to', filters.date_to)
            if (filters?.session_type) params.append('session_type', filters.session_type)
            if (filters?.patient_id) params.append('patient_id', filters.patient_id)
            if (filters?.clinician_id) params.append('clinician_id', filters.clinician_id)
            if (filters?.has_mse !== undefined) params.append('has_mse', String(filters.has_mse))
            if (filters?.has_diagnosis !== undefined) params.append('has_diagnosis', String(filters.has_diagnosis))
            if (filters?.has_management_plan !== undefined) params.append('has_management_plan', String(filters.has_management_plan))

            const query = params.toString()
            const url = `${config.public.apiBase}/reports/consulting-data-query${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<any>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to generate consulting data query report',
                data: [],
            }
        }
    }

    /**
     * Trend Report
     */
    const trends = async (filters?: {
        date_from?: string
        date_to?: string
        group_by?: string
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.date_from) params.append('date_from', filters.date_from)
            if (filters?.date_to) params.append('date_to', filters.date_to)
            if (filters?.group_by) params.append('group_by', filters.group_by)

            const query = params.toString()
            const url = `${config.public.apiBase}/reports/trends${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<any>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to generate trend report',
            }
        }
    }

    /**
     * Total Cases Report
     */
    const totalCases = async (filters?: {
        date_from?: string
        date_to?: string
    }) => {
        try {
            const params = new URLSearchParams()
            if (filters?.date_from) params.append('date_from', filters.date_from)
            if (filters?.date_to) params.append('date_to', filters.date_to)

            const query = params.toString()
            const url = `${config.public.apiBase}/reports/total-cases${query ? `?${query}` : ''}`

            const response = await $fetch<ApiResponse<any>>(url, {
                headers: {
                    Authorization: `Bearer ${token.value}`,
                },
            })

            return { success: true, data: response.data }
        } catch (error: any) {
            return {
                success: false,
                error: error.data?.error?.message || 'Failed to generate total cases report',
            }
        }
    }

    return {
        clientList,
        clientQuery,
        consultingHistory,
        nextVisits,
        consultingDataQuery,
        trends,
        totalCases,
    }
}

