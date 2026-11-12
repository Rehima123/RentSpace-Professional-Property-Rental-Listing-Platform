import { defineStore } from 'pinia'
import { ref } from 'vue'

let toastIdCounter = 0

export const useUiStore = defineStore('ui', () => {
  const toasts = ref([])
  const confirmDialog = ref(null)

  function toast(message, type = 'success', duration = 4000) {
    const id = ++toastIdCounter
    toasts.value.push({ id, message, type })
    setTimeout(() => removeToast(id), duration)
  }

  function success(message) { toast(message, 'success') }
  function error(message)   { toast(message, 'error', 5000) }
  function info(message)    { toast(message, 'info') }
  function warn(message)    { toast(message, 'warning') }

  function removeToast(id) {
    toasts.value = toasts.value.filter((t) => t.id !== id)
  }

  function confirm({ title, message, confirmText = 'Confirm', cancelText = 'Cancel', onConfirm }) {
    confirmDialog.value = { title, message, confirmText, cancelText, onConfirm }
  }

  function closeConfirm() {
    confirmDialog.value = null
  }

  return { toasts, confirmDialog, toast, success, error, info, warn, removeToast, confirm, closeConfirm }
})
