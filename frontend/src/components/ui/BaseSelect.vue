<template>
  <div class="w-full">
    <label v-if="label" :for="selectId" class="label">{{ label }}</label>
    <select
      :id="selectId"
      :value="modelValue"
      :disabled="disabled"
      :class="['input cursor-pointer', { 'input-error': !!error }]"
      @change="$emit('update:modelValue', $event.target.value)"
    >
      <option v-if="placeholder" value="">{{ placeholder }}</option>
      <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
    </select>
    <p v-if="error" class="mt-1.5 text-xs text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
defineProps({
  modelValue: { default: '' },
  label:      { type: String, default: null },
  options:    { type: Array, default: () => [] }, // [{ value, label }]
  placeholder:{ type: String, default: 'Select...' },
  error:      { type: String, default: null },
  disabled:   { type: Boolean, default: false },
})
defineEmits(['update:modelValue'])
const selectId = computed(() => `sel-${Math.random().toString(36).slice(2, 8)}`)
</script>
