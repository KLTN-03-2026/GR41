import { toast as sonnerToast } from 'vue-sonner'

export function useToast() {
  return {
    success: (msg) => sonnerToast.success(msg),
    error: (msg) => sonnerToast.error(msg),
    info: (msg) => sonnerToast.message(msg),
  }
}
