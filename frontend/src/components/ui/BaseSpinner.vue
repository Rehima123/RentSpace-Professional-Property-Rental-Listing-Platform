<template>
  <div :class="['flex items-center justify-center', fullPage && 'min-h-[400px]']">
    <div :class="['animate-spin rounded-full border-2 border-gray-200', borderClass, sizeClass]" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  size:     { type: String, default: 'md' },
  color:    { type: String, default: 'navy' },
  fullPage: { type: Boolean, default: false },
})

const sizeClass = computed(() => ({
  sm: 'h-5 w-5',
  md: 'h-8 w-8',
  lg: 'h-12 w-12',
}[props.size] ?? 'h-8 w-8'))

const borderClass = computed(() => ({
  navy: 'border-t-navy-600',
  teal: 'border-t-teal-500',
  white:'border-t-white',
}[props.color] ?? 'border-t-navy-600'))
</script>
