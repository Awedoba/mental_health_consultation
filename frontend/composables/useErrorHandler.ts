import type { ErrorResponse } from '~/types'

export const useErrorHandler = () => {
    const { showToast } = useToast()
    const config = useRuntimeConfig()

    /**
     * Parse API error and extract user-friendly message
     */
    const parseApiError = (error: any): string => {
        // Handle Nuxt/Fetch errors
        if (error?.data?.error) {
            const apiError = error.data.error as ErrorResponse['error']

            // Return specific validation errors if available
            if (apiError.errors) {
                const firstError = Object.values(apiError.errors)[0]
                if (firstError && Array.isArray(firstError) && firstError.length > 0) {
                    return firstError[0] || 'Validation error'
                }
            }

            // Return main error message
            if (apiError.message) {
                return apiError.message
            }
        }

        // Handle axios-style errors
        if (error?.response?.data?.error?.message) {
            return error.response.data.error.message
        }

        // Handle network errors
        if (error?.message) {
            if (error.message.includes('fetch') || error.message.includes('network')) {
                return 'Network error. Please check your connection and try again.'
            }
            return error.message
        }

        return 'An unexpected error occurred. Please try again.'
    }

    /**
     * Get all validation errors from API response
     */
    const getValidationErrors = (error: any): Record<string, string[]> => {
        if (error?.data?.error?.errors) {
            return error.data.error.errors
        }
        if (error?.response?.data?.error?.errors) {
            return error.response.data.error.errors
        }
        return {}
    }

    /**
     * Handle API error with toast notification
     */
    const handleApiError = (error: any, context?: string) => {
        const message = parseApiError(error)
        const fullMessage = context ? `${context}: ${message}` : message

        // Log detailed error in development
        if (config.public.dev) {
            console.error('API Error:', {
                context,
                error,
                message,
                validationErrors: getValidationErrors(error),
            })
        }

        // Show toast notification
        showToast('error', fullMessage)

        return {
            message,
            validationErrors: getValidationErrors(error),
        }
    }

    /**
     * Handle generic error with toast notification
     */
    const handleError = (error: any, context?: string) => {
        const message = error?.message || 'An unexpected error occurred'
        const fullMessage = context ? `${context}: ${message}` : message

        // Log in development
        if (config.public.dev) {
            console.error('Error:', { context, error, message })
        }

        // Show toast notification
        showToast('error', fullMessage)

        return { message }
    }

    /**
     * Show success message
     */
    const showSuccess = (message: string) => {
        showToast('success', message)
    }

    /**
     * Show warning message
     */
    const showWarning = (message: string) => {
        showToast('warning', message)
    }

    /**
     * Show info message
     */
    const showInfo = (message: string) => {
        showToast('info', message)
    }

    return {
        parseApiError,
        getValidationErrors,
        handleApiError,
        handleError,
        showSuccess,
        showWarning,
        showInfo,
    }
}
