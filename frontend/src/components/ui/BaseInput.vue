<template>
  <div class="w-full">
    <label v-if="label" :for="inputId" class="label">{{ label }} <span v-if="required" class="text-red-500">*</span></label>
    <div class="relative">
      <span v-if="$slots.prefix" class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
        <slot name="prefix" />
      </span>
      <component
        :is="type === 'textarea' ? 'textarea' : 'input'"
        :id="inputId"
        v-bind="$attrs"
        :type="type !== 'textarea' ? type : undefined"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :rows="type === 'textarea' ? rows : undefined"
        :class="[
          'input',
          { 'input-error': !!error },
          { 'pl-10': $slots.prefix },
          { 'pr-10': $slots.suffix },
          { 'resize-none': type === 'textarea' },
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
      />
      <span v-if="$slots.suffix" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 pointer-events-none">
        <slot name="suffix" />
      </span>
    </div>
    <p v-if="error" class="mt-1.5 text-xs text-red-600">{{ error }}</p>
    <p v-else-if="hint" class="mt-1.5 text-xs text-gray-500">{{ hint }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { default: '' },
  label:      { type: String, default: null },
  type:       { type: String, default: 'text' },
  placeholder:{ type: String, default: '' },
  error:      { type: String, default: null },
  hint:       { type: String, default: null },
  disabled:   { type: Boolean, default: false },
  required:   { type: Boolean, default: false },
  rows:       { type: Number, default: 4 },
  id:         { type: String, default: null },
})
defineEmits(['update:modelValue', 'blur'])
defineOptions({ inheritAttrs: false })

const inputId = computed(() => props.id ?? `input-${Math.random().toString(36).slice(2, 8)}`)
</script>
