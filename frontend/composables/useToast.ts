import type { ToastNotification, ToastType } from '~/types'

const toasts = ref<ToastNotification[]>([])
let toastIdCounter = 0

export const useToast = () => {
    const showToast = (
        type: ToastType,
        message: string,
        duration: number = 5000
    ) => {
        const id = `toast-${++toastIdCounter}`
        const toast: ToastNotification = {
            id,
            type,
            message,
            duration,
        }

        toasts.value.push(toast)

        // Auto-dismiss after duration
        if (duration > 0) {
            setTimeout(() => {
                removeToast(id)
            }, duration)
        }

        return id
    }

    const removeToast = (id: string) => {
        const index = toasts.value.findIndex((t) => t.id === id)
        if (index > -1) {
            toasts.value.splice(index, 1)
        }
    }

    const clearAll = () => {
        toasts.value = []
    }

    return {
        toasts: readonly(toasts),
        showToast,
        removeToast,
        clearAll,
    }
}
