<template>
  <DashboardLayout :breadcrumbs="[{ label: 'Saved Properties' }]">
    <div class="mb-6">
      <h1 class="page-title">Saved Properties</h1>
      <p class="page-subtitle">Properties you've added to your favorites</p>
    </div>

    <BaseSpinner v-if="loading" :full-page="true" />

    <template v-else-if="favorites.length">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="fav in favorites" :key="fav.id" class="relative">
          <RouterLink :to="{ name: 'property.show', params: { id: fav.property?.id } }">
            <PropertyCard :property="fav.property" :show-favorite="true" :is-favorited="true" @toggle-favorite="removeFavorite(fav.property?.id, fav.id)" />
          </RouterLink>
        </div>
      </div>
      <Pagination :meta="pagination" @page-change="goToPage" />
    </template>

    <EmptyState v-else :icon="HeartIcon" title="No saved properties" description="Browse listings and tap the heart icon to save properties you like.">
      <RouterLink :to="{ name: 'properties' }" class="btn btn-primary btn-sm mt-4">Browse Properties</RouterLink>
    </EmptyState>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardLayout from '@/components/layout/DashboardLayout.vue'
import PropertyCard    from '@/components/property/PropertyCard.vue'
import BaseSpinner from '@/components/ui/BaseSpinner.vue'
import EmptyState  from '@/components/ui/EmptyState.vue'
import Pagination  from '@/components/ui/Pagination.vue'
import { Heart as HeartIcon } from 'lucide-vue-next'
import favoriteService from '@/services/favorite.js'
import { useUiStore }  from '@/stores/ui.js'

const ui         = useUiStore()
const favorites  = ref([])
const pagination = ref(null)
const loading    = ref(true)

async function fetch(page = 1) {
  loading.value = true
  try {
    const { data } = await favoriteService.list({ page, per_page: 12 })
    favorites.value  = data.data
    pagination.value = data.meta
  } finally { loading.value = false }
}

async function removeFavorite(propertyId) {
  await favoriteService.toggle(propertyId)
  favorites.value = favorites.value.filter(f => f.property?.id !== propertyId)
  ui.info('Removed from favorites')
}

function goToPage(p) { fetch(p) }
onMounted(() => fetch())
</script>
