<template>
  <span :class="['badge', variantClass]">
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  variant: { type: String, default: 'default' },
  status:  { type: String, default: null },
})

const variantClass = computed(() => {
  if (props.status) {
    return {
      pending:     'badge-pending',
      approved:    'badge-approved',
      rejected:    'badge-rejected',
      rented:      'badge-rented',
      unavailable: 'badge-unavailable',
      accepted:    'badge-accepted',
    }[props.status] ?? 'bg-gray-100 text-gray-600'
  }
  return {
    default:  'bg-gray-100 text-gray-700',
    primary:  'bg-navy-100 text-navy-700',
    teal:     'bg-teal-100 text-teal-700',
    success:  'bg-green-100 text-green-700',
    warning:  'bg-amber-100 text-amber-700',
    danger:   'bg-red-100 text-red-700',
  }[props.variant] ?? 'bg-gray-100 text-gray-700'
})
</script>
