<template>
  <div class="flex flex-col items-center justify-center py-16 text-center px-4">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
      <component :is="icon" :size="28" class="text-gray-400" />
    </div>
    <h3 class="text-lg font-semibold text-gray-700 mb-1">{{ title }}</h3>
    <p class="text-sm text-gray-500 max-w-xs">{{ description }}</p>
    <slot />
  </div>
</template>

<script setup>
import { Inbox as InboxIcon } from 'lucide-vue-next'
defineProps({
  icon:        { default: InboxIcon },
  title:       { type: String, default: 'Nothing here yet' },
  description: { type: String, default: 'There are no items to display at the moment.' },
})
</script>
