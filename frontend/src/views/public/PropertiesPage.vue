<template>
  <PublicLayout>
    <div class="bg-navy-800 py-10">
      <div class="container-app">
        <h1 class="text-3xl font-bold text-white mb-1">Browse Properties</h1>
        <p class="text-navy-300 text-sm">{{ pagination?.total ?? 0 }} properties found in Ethiopia</p>
      </div>
    </div>

    <div class="container-app py-8">
      <div class="flex flex-col lg:flex-row gap-6">

        <!-- Sidebar Filters -->
        <aside class="lg:w-72 shrink-0">
          <div class="card p-5 sticky top-20">
            <div class="flex items-center justify-between mb-4">
              <h2 class="font-semibold text-gray-900">Filters</h2>
              <button
                v-if="hasActiveFilters"
                class="text-xs text-red-500 hover:underline flex items-center gap-1"
                @click="clearAll"
              >
                <XIcon :size="12" /> Clear all
              </button>
            </div>

            <div class="space-y-5">
              <!-- Search -->
              <BaseInput
                v-model="filters.search"
                placeholder="Search by keyword..."
                @keydown.enter="applyFilters"
              >
                <template #prefix><SearchIcon :size="14" /></template>
              </BaseInput>

              <!-- City -->
              <BaseSelect
                v-model="filters.city"
                label="City"
                :options="cityOptions"
                placeholder="Any city"
              />

              <!-- Property Type -->
              <BaseSelect
                v-model="filters.type"
                label="Property Type"
                :options="PROPERTY_TYPES"
                placeholder="Any type"
              />

              <!-- Price Range -->
              <div>
                <p class="label">Price Range (ETB)</p>
                <div class="grid grid-cols-2 gap-2">
                  <input
                    v-model="filters.min_price"
                    type="number"
                    min="0"
                    placeholder="Min"
                    class="input text-sm"
                  />
                  <input
                    v-model="filters.max_price"
                    type="number"
                    min="0"
                    placeholder="Max"
                    class="input text-sm"
                  />
                </div>
              </div>

              <!-- Bedrooms -->
              <BaseSelect
                v-model="filters.bedrooms"
                label="Bedrooms"
                :options="BEDROOM_OPTIONS"
                placeholder="Any"
              />

              <!-- Furnishing -->
              <BaseSelect
                v-model="filters.furnishing_status"
                label="Furnishing"
                :options="FURNISHING_STATUSES"
                placeholder="Any"
              />

              <BaseButton class="w-full" @click="applyFilters" :loading="loading">
                <SlidersHorizontalIcon :size="15" /> Apply Filters
              </BaseButton>
            </div>
          </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 min-w-0">
          <!-- Toolbar -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <p class="text-sm text-gray-500">
              <span v-if="!loading" class="font-medium text-gray-800">{{ pagination?.total ?? 0 }}</span>
              <span v-else class="skeleton inline-block w-8 h-4 mr-1" />
              properties found
            </p>
            <div class="flex items-center gap-2">
              <label class="text-sm text-gray-500 whitespace-nowrap">Sort by:</label>
              <select
                v-model="filters.sort"
                class="input text-sm py-1.5 w-auto"
                @change="applyFilters"
              >
                <option v-for="o in SORT_OPTIONS" :key="o.value" :value="o.value">{{ o.label }}</option>
              </select>
              <!-- Grid/List toggle -->
              <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                <button
                  :class="['p-2 transition-colors', viewMode === 'grid' ? 'bg-navy-600 text-white' : 'text-gray-400 hover:text-gray-600']"
                  @click="viewMode = 'grid'"
                ><LayoutGridIcon :size="15" /></button>
                <button
                  :class="['p-2 transition-colors', viewMode === 'list' ? 'bg-navy-600 text-white' : 'text-gray-400 hover:text-gray-600']"
                  @click="viewMode = 'list'"
                ><ListIcon :size="15" /></button>
              </div>
            </div>
          </div>

          <!-- Active filter chips -->
          <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 mb-4">
            <span
              v-for="chip in activeChips"
              :key="chip.key"
              class="inline-flex items-center gap-1.5 px-3 py-1 bg-navy-50 text-navy-700 text-xs font-medium rounded-full border border-navy-200"
            >
              {{ chip.label }}
              <button @click="clearFilter(chip.key)" class="hover:text-red-500">
                <XIcon :size="10" />
              </button>
            </span>
          </div>

          <!-- Loading skeleton -->
          <div v-if="loading" :class="['grid gap-5', viewMode === 'grid' ? 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3' : 'grid-cols-1']">
            <div v-for="i in 9" :key="i" class="card overflow-hidden">
              <div class="skeleton aspect-[16/10]" />
              <div class="p-4 space-y-3">
                <div class="skeleton h-5 w-3/4" />
                <div class="skeleton h-4 w-1/2" />
                <div class="skeleton h-4 w-full" />
              </div>
            </div>
          </div>

          <!-- Properties grid/list -->
          <div
            v-else-if="properties.length"
            :class="['grid gap-5', viewMode === 'grid' ? 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3' : 'grid-cols-1']"
          >
            <RouterLink
              v-for="p in properties"
              :key="p.id"
              :to="{ name: 'property.show', params: { id: p.id } }"
              class="block"
            >
              <PropertyCard
                :property="p"
                :show-favorite="auth.isAuthenticated"
                :is-favorited="favoriteIds.has(p.id)"
                @toggle-favorite="toggleFavorite"
              />
            </RouterLink>
          </div>

          <!-- Empty state -->
          <EmptyState
            v-else
            :icon="SearchIcon"
            title="No properties found"
            description="Try adjusting your filters or searching for a different keyword."
          >
            <BaseButton variant="secondary" class="mt-4" @click="clearAll">Clear Filters</BaseButton>
          </EmptyState>

          <!-- Pagination -->
          <Pagination :meta="pagination" @page-change="goToPage" />
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PublicLayout  from '@/components/layout/PublicLayout.vue'
import PropertyCard  from '@/components/property/PropertyCard.vue'
import BaseInput     from '@/components/ui/BaseInput.vue'
import BaseSelect    from '@/components/ui/BaseSelect.vue'
import BaseButton    from '@/components/ui/BaseButton.vue'
import EmptyState    from '@/components/ui/EmptyState.vue'
import Pagination    from '@/components/ui/Pagination.vue'
import {
  Search               as SearchIcon,
  X                    as XIcon,
  SlidersHorizontal    as SlidersHorizontalIcon,
  LayoutGrid           as LayoutGridIcon,
  List                 as ListIcon,
} from 'lucide-vue-next'

import propertyService  from '@/services/property.js'
import favoriteService  from '@/services/favorite.js'
import { useAuthStore } from '@/stores/auth.js'
import { useUiStore }   from '@/stores/ui.js'
import { CITIES, PROPERTY_TYPES, FURNISHING_STATUSES, BEDROOM_OPTIONS, SORT_OPTIONS } from '@/utils/constants.js'

const route  = useRoute()
const router = useRouter()
const auth   = useAuthStore()
const ui     = useUiStore()

const properties  = ref([])
const pagination  = ref(null)
const loading     = ref(false)
const viewMode    = ref('grid')
const favoriteIds = ref(new Set())
const dynamicCities = ref([]) // Loaded from API; falls back to constants

const filters = reactive({
  search: '',
  city: '',
  type: '',
  min_price: '',
  max_price: '',
  bedrooms: '',
  furnishing_status: '',
  sort: 'newest',
  page: 1,
  per_page: 12,
})

const cityOptions = computed(() => {
  const source = dynamicCities.value.length
    ? dynamicCities.value.map(c => ({ value: c.city, label: `${c.city} (${c.properties_count})` }))
    : CITIES.map(c => ({ value: c, label: c }))
  return source
})

// Active filter chips for display
const activeChips = computed(() => {
  const chips = []
  if (filters.search)           chips.push({ key: 'search',           label: `"${filters.search}"` })
  if (filters.city)             chips.push({ key: 'city',             label: filters.city })
  if (filters.type)             chips.push({ key: 'type',             label: PROPERTY_TYPES.find(t => t.value === filters.type)?.label })
  if (filters.min_price)        chips.push({ key: 'min_price',        label: `Min ETB ${filters.min_price}` })
  if (filters.max_price)        chips.push({ key: 'max_price',        label: `Max ETB ${filters.max_price}` })
  if (filters.bedrooms)         chips.push({ key: 'bedrooms',         label: `${filters.bedrooms}+ beds` })
  if (filters.furnishing_status)chips.push({ key: 'furnishing_status',label: FURNISHING_STATUSES.find(f => f.value === filters.furnishing_status)?.label })
  return chips
})

const hasActiveFilters = computed(() => activeChips.value.length > 0)

function clearFilter(key) {
  filters[key] = ''
  filters.page = 1
  fetchProperties()
}

function clearAll() {
  Object.assign(filters, { search: '', city: '', type: '', min_price: '', max_price: '', bedrooms: '', furnishing_status: '', sort: 'newest', page: 1 })
  fetchProperties()
}

function applyFilters() {
  filters.page = 1
  fetchProperties()
  syncUrl()
}

function goToPage(page) {
  filters.page = page
  fetchProperties()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function syncUrl() {
  const q = {}
  if (filters.search)            q.search = filters.search
  if (filters.city)              q.city = filters.city
  if (filters.type)              q.type = filters.type
  if (filters.min_price)         q.min_price = filters.min_price
  if (filters.max_price)         q.max_price = filters.max_price
  if (filters.bedrooms)          q.bedrooms = filters.bedrooms
  if (filters.furnishing_status) q.furnishing_status = filters.furnishing_status
  if (filters.sort !== 'newest') q.sort = filters.sort
  router.replace({ query: q })
}

function loadFromUrl() {
  const q = route.query
  if (q.search)            filters.search = q.search
  if (q.city)              filters.city = q.city
  if (q.type)              filters.type = q.type
  if (q.min_price)         filters.min_price = q.min_price
  if (q.max_price)         filters.max_price = q.max_price
  if (q.bedrooms)          filters.bedrooms = q.bedrooms
  if (q.furnishing_status) filters.furnishing_status = q.furnishing_status
  if (q.sort)              filters.sort = q.sort
}

async function fetchProperties() {
  loading.value = true
  try {
    const params = {}
    if (filters.search)            params.search = filters.search
    if (filters.city)              params.city = filters.city
    if (filters.type)              params.type = filters.type
    if (filters.min_price)         params.min_price = filters.min_price
    if (filters.max_price)         params.max_price = filters.max_price
    if (filters.bedrooms)          params.bedrooms = filters.bedrooms
    if (filters.furnishing_status) params.furnishing_status = filters.furnishing_status
    params.sort     = filters.sort
    params.page     = filters.page
    params.per_page = filters.per_page

    const { data } = await propertyService.list(params)
    properties.value = data.data
    pagination.value = data.meta
  } catch {
    ui.error('Failed to load properties.')
  } finally {
    loading.value = false
  }
}

async function fetchFavorites() {
  if (!auth.isAuthenticated) return
  try {
    const { data } = await favoriteService.list({ per_page: 100 })
    favoriteIds.value = new Set(data.data.map(f => f.property?.id).filter(Boolean))
  } catch { /* silent */ }
}

async function toggleFavorite(propertyId) {
  if (!auth.isAuthenticated) {
    router.push({ name: 'login' })
    return
  }
  try {
    const { data } = await favoriteService.toggle(propertyId)
    if (data.favorited) {
      favoriteIds.value.add(propertyId)
      ui.success('Added to favorites')
    } else {
      favoriteIds.value.delete(propertyId)
      ui.info('Removed from favorites')
    }
    favoriteIds.value = new Set(favoriteIds.value) // trigger reactivity
  } catch {
    ui.error('Could not update favorites.')
  }
}

async function fetchCities() {
  try {
    const { data } = await propertyService.cities()
    if (data.data?.length) dynamicCities.value = data.data
  } catch { /* silent — fall back to static list */ }
}

onMounted(() => {
  loadFromUrl()
  fetchProperties()
  fetchFavorites()
  fetchCities()
})
</script>
