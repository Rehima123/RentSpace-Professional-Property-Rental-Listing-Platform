<template>
  <div v-if="meta && meta.last_page > 1" class="flex items-center justify-between mt-8">
    <p class="text-sm text-gray-500">
      Showing <span class="font-medium">{{ meta.from }}</span>–<span class="font-medium">{{ meta.to }}</span>
      of <span class="font-medium">{{ meta.total }}</span> results
    </p>

    <div class="flex items-center gap-1">
      <button
        :disabled="meta.current_page === 1"
        class="btn btn-ghost btn-sm disabled:opacity-40"
        @click="$emit('page-change', meta.current_page - 1)"
      >
        <ChevronLeftIcon :size="16" />
      </button>

      <template v-for="page in visiblePages" :key="page">
        <span v-if="page === '...'" class="px-2 text-gray-400">...</span>
        <button
          v-else
          :class="['btn btn-sm font-medium', page === meta.current_page ? 'btn-primary' : 'btn-ghost']"
          @click="$emit('page-change', page)"
        >
          {{ page }}
        </button>
      </template>

      <button
        :disabled="meta.current_page === meta.last_page"
        class="btn btn-ghost btn-sm disabled:opacity-40"
        @click="$emit('page-change', meta.current_page + 1)"
      >
        <ChevronRightIcon :size="16" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ChevronLeft as ChevronLeftIcon, ChevronRight as ChevronRightIcon } from 'lucide-vue-next'

const props = defineProps({
  meta: { type: Object, default: null },
})
defineEmits(['page-change'])

const visiblePages = computed(() => {
  if (!props.meta) return []
  const { current_page: cur, last_page: last } = props.meta
  const pages = []
  if (last <= 7) {
    for (let i = 1; i <= last; i++) pages.push(i)
  } else {
    pages.push(1)
    if (cur > 3) pages.push('...')
    for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i)
    if (cur < last - 2) pages.push('...')
    pages.push(last)
  }
  return pages
})
</script>
