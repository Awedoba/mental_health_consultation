import type { Patient } from '~/types'

export const usePatients = () => {
  const config = useRuntimeConfig()
  const { token } = useAuth()

  const list = async (filters?: {
    search?: string
    is_active?: boolean
    age_min?: number
    age_max?: number
  }) => {
    try {
      const params = new URLSearchParams()
      if (filters?.search) params.append('search', filters.search)
      if (filters?.is_active !== undefined) params.append('is_active', String(filters.is_active))
      if (filters?.age_min) params.append('age_min', String(filters.age_min))
      if (filters?.age_max) params.append('age_max', String(filters.age_max))

      const query = params.toString()
      const url = `${config.public.apiBase}/patients${query ? `?${query}` : ''}`

      const { data, meta } = await $fetch<{
        data: Patient[]
        meta: { pagination: any }
        message: string
      }>(url, {
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      })

      return { success: true, data, meta }
    } catch (error: any) {
      return {
        success: false,
        error: error.data?.error?.message || 'Failed to fetch patients',
      }
    }
  }

  const show = async (id: string) => {
    try {
      const { data } = await $fetch<{
        data: Patient
      }>(`${config.public.apiBase}/patients/${id}`, {
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      })

      return { success: true, data }
    } catch (error: any) {
      return {
        success: false,
        error: error.data?.error?.message || 'Failed to fetch patient',
      }
    }
  }

  const create = async (patientData: any) => {
    try {
      const { data } = await $fetch<{
        data: Patient
        message: string
      }>(`${config.public.apiBase}/patients`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
        body: patientData,
      })

      return { success: true, data }
    } catch (error: any) {
      return {
        success: false,
        error: error.data?.error?.message || 'Failed to create patient',
        errors: error.data?.error?.errors,
      }
    }
  }

  const update = async (id: string, patientData: any) => {
    try {
      const { data } = await $fetch<{
        data: Patient
        message: string
      }>(`${config.public.apiBase}/patients/${id}`, {
        method: 'PUT',
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
        body: patientData,
      })

      return { success: true, data }
    } catch (error: any) {
      return {
        success: false,
        error: error.data?.error?.message || 'Failed to update patient',
        errors: error.data?.error?.errors,
      }
    }
  }

  return {
    list,
    show,
    create,
    update,
  }
}

