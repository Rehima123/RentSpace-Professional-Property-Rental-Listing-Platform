<template>
  <div class="fixed bottom-5 right-5 z-[100] flex flex-col gap-3 max-w-sm w-full pointer-events-none">
    <TransitionGroup name="slide-up">
      <div
        v-for="toast in uiStore.toasts"
        :key="toast.id"
        :class="['pointer-events-auto flex items-start gap-3 px-4 py-3.5 rounded-xl shadow-lg border backdrop-blur-sm', toastClass(toast.type)]"
      >
        <component :is="toastIcon(toast.type)" :size="18" class="shrink-0 mt-0.5" />
        <span class="text-sm font-medium flex-1">{{ toast.message }}</span>
        <button class="shrink-0 opacity-60 hover:opacity-100" @click="uiStore.removeToast(toast.id)">
          <XIcon :size="14" />
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useUiStore } from '@/stores/ui.js'
import {
  CheckCircle2  as CheckCircle2Icon,
  AlertCircle   as AlertCircleIcon,
  Info          as InfoIcon,
  AlertTriangle as AlertTriangleIcon,
  X             as XIcon,
} from 'lucide-vue-next'

const uiStore = useUiStore()

function toastClass(type) {
  return {
    success: 'bg-green-50 border-green-200 text-green-800',
    error:   'bg-red-50 border-red-200 text-red-800',
    info:    'bg-blue-50 border-blue-200 text-blue-800',
    warning: 'bg-amber-50 border-amber-200 text-amber-800',
  }[type] ?? 'bg-white border-gray-200 text-gray-800'
}

function toastIcon(type) {
  return {
    success: CheckCircle2Icon,
    error:   AlertCircleIcon,
    info:    InfoIcon,
    warning: AlertTriangleIcon,
  }[type] ?? InfoIcon
}
</script>
