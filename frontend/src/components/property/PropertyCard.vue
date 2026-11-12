<template>
  <article class="card overflow-hidden group hover:shadow-card-hover transition-all duration-300">
    <!-- Image -->
    <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
      <img
        v-if="property.primary_image"
        :src="property.primary_image"
        :alt="property.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
      />
      <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
        <HomeIcon :size="40" class="text-gray-300" />
      </div>

      <!-- Status badge -->
      <div class="absolute top-3 left-3">
        <BaseBadge :status="property.status">{{ capitalize(property.status) }}</BaseBadge>
      </div>

      <!-- Favorite button -->
      <button
        v-if="showFavorite"
        :class="[
          'absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-150',
          isFavorited ? 'bg-red-500 text-white' : 'bg-white/80 backdrop-blur-sm text-gray-500 hover:bg-white hover:text-red-500',
        ]"
        :title="isFavorited ? 'Remove from favorites' : 'Add to favorites'"
        @click.prevent="$emit('toggle-favorite', property.id)"
      >
        <HeartIcon :size="14" :fill="isFavorited ? 'currentColor' : 'none'" />
      </button>

      <!-- Featured ribbon -->
      <div v-if="property.is_featured" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent h-10 pointer-events-none" />
    </div>

    <!-- Content -->
    <div class="p-4">
      <!-- Price -->
      <div class="flex items-baseline justify-between mb-2">
        <p class="text-lg font-bold text-navy-700">
          {{ formatPricePerMonth(property.price) }}
        </p>
        <span class="text-xs font-medium text-teal-600 capitalize bg-teal-50 px-2 py-0.5 rounded-full">
          {{ typeLabel }}
        </span>
      </div>

      <!-- Title -->
      <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-1 group-hover:text-navy-600 transition-colors line-clamp-2">
        {{ property.title }}
      </h3>

      <!-- Location -->
      <p class="flex items-center gap-1 text-xs text-gray-500 mb-3">
        <MapPinIcon :size="12" class="shrink-0" />
        {{ property.city }}<span v-if="property.sub_city">, {{ property.sub_city }}</span>
      </p>

      <!-- Specs row -->
      <div class="flex items-center gap-3 text-xs text-gray-500 border-t border-gray-100 pt-3">
        <span v-if="property.bedrooms != null" class="flex items-center gap-1">
          <BedDoubleIcon :size="13" /> {{ property.bedrooms }} bed
        </span>
        <span class="flex items-center gap-1">
          <BathIcon :size="13" /> {{ property.bathrooms }} bath
        </span>
        <span v-if="property.size_sqm" class="flex items-center gap-1">
          <SquareIcon :size="13" /> {{ property.size_sqm }}m²
        </span>
        <span class="ml-auto capitalize">{{ property.furnishing_status?.replace('-', '\u00A0') }}</span>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { Home as HomeIcon, Heart as HeartIcon, MapPin as MapPinIcon, BedDouble as BedDoubleIcon, Bath as BathIcon, Square as SquareIcon } from 'lucide-vue-next'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import { formatPricePerMonth, capitalize } from '@/utils/format.js'
import { PROPERTY_TYPES } from '@/utils/constants.js'

const props = defineProps({
  property:     { type: Object, required: true },
  showFavorite: { type: Boolean, default: true },
  isFavorited:  { type: Boolean, default: false },
})
defineEmits(['toggle-favorite'])

const typeLabel = computed(() =>
  PROPERTY_TYPES.find(t => t.value === props.property.type)?.label ?? props.property.type
)
</script>
