<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="closeable && $emit('update:modelValue', false)"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" />

        <!-- Dialog box -->
        <div
          class="modal-box relative bg-white rounded-2xl shadow-2xl w-full z-10"
          :class="sizeClass"
        >
          <!-- Header -->
          <div v-if="title || $slots.header" class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
            <slot name="header">
              <h3 class="text-lg font-semibold text-navy-900">{{ title }}</h3>
            </slot>
            <button
              v-if="closeable"
              class="text-gray-400 hover:text-gray-600 transition-colors"
              @click="$emit('update:modelValue', false)"
            >
              <XIcon :size="20" />
            </button>
          </div>

          <!-- Body -->
          <div class="px-6 py-5">
            <slot />
          </div>

          <!-- Footer -->
          <div v-if="$slots.footer" class="px-6 pb-5 pt-2">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { X as XIcon } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title:      { type: String, default: null },
  size:       { type: String, default: 'md' },
  closeable:  { type: Boolean, default: true },
})
defineEmits(['update:modelValue'])

const sizeClass = computed(() => ({
  sm: 'max-w-sm',
  md: 'max-w-lg',
  lg: 'max-w-2xl',
  xl: 'max-w-4xl',
}[props.size] ?? 'max-w-lg'))
</script>
