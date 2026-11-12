<template>
  <div class="card p-5 flex items-start gap-4">
    <div :class="['w-11 h-11 rounded-xl flex items-center justify-center shrink-0', iconBg]">
      <component :is="icon" :size="20" :class="iconColor" />
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-xs font-medium text-gray-500 mb-0.5">{{ label }}</p>
      <p class="text-2xl font-black text-gray-900">{{ value }}</p>
      <p v-if="sub" class="text-xs text-gray-400 mt-0.5">{{ sub }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label:   { type: String,   required: true },
  value:   { type: [String, Number], required: true },
  icon:    { required: true },
  color:   { type: String, default: 'navy' }, // navy | teal | amber | green | red | blue
  sub:     { type: String, default: null },
})

const palettes = {
  navy:  { bg: 'bg-navy-50',  color: 'text-navy-600'  },
  teal:  { bg: 'bg-teal-50',  color: 'text-teal-600'  },
  amber: { bg: 'bg-amber-50', color: 'text-amber-600' },
  green: { bg: 'bg-green-50', color: 'text-green-600' },
  red:   { bg: 'bg-red-50',   color: 'text-red-600'   },
  blue:  { bg: 'bg-blue-50',  color: 'text-blue-600'  },
}

const iconBg    = computed(() => palettes[props.color]?.bg    ?? palettes.navy.bg)
const iconColor = computed(() => palettes[props.color]?.color ?? palettes.navy.color)
</script>
