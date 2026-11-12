<template>
  <component
    :is="tag"
    v-bind="attrs"
    :class="[variantClass, sizeClass, 'btn', { 'opacity-50 cursor-not-allowed': disabled || loading }]"
    :disabled="disabled || loading"
    @click="$emit('click', $event)"
  >
    <component :is="Loader2Icon" v-if="loading" :size="16" class="animate-spin" />
    <slot v-else />
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { Loader2 as Loader2Icon } from 'lucide-vue-next'

const props = defineProps({
  variant: { type: String, default: 'primary' }, // primary | secondary | teal | danger | ghost
  size:    { type: String, default: 'md' },       // sm | md | lg
  tag:     { type: String, default: 'button' },
  href:    { type: String, default: null },
  to:      { type: [String, Object], default: null },
  disabled:{ type: Boolean, default: false },
  loading: { type: Boolean, default: false },
})

defineEmits(['click'])

const attrs = computed(() => {
  if (props.tag === 'a') return { href: props.href }
  if (props.tag === 'RouterLink') return { to: props.to }
  return {}
})

const variantClass = computed(() => ({
  primary:   'btn-primary',
  secondary: 'btn-secondary',
  teal:      'btn-teal',
  danger:    'btn-danger',
  ghost:     'btn-ghost',
}[props.variant] ?? 'btn-primary'))

const sizeClass = computed(() => ({
  sm: 'btn-sm',
  md: '',
  lg: 'btn-lg',
}[props.size] ?? ''))
</script>
