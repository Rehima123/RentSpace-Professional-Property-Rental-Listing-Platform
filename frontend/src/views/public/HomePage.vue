<template>
  <PublicLayout>
    <!-- Hero Section -->
    <section class="relative bg-navy-900 text-white overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-navy-900 via-navy-800 to-teal-900 opacity-90" />
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-20 w-72 h-72 bg-teal-400 rounded-full blur-3xl" />
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-navy-400 rounded-full blur-3xl" />
      </div>

      <div class="relative container-app py-20 md:py-28">
        <div class="max-w-3xl">
          <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-1.5 mb-6">
            <span class="w-2 h-2 bg-teal-400 rounded-full animate-pulse" />
            <span class="text-sm text-teal-300 font-medium">Ethiopia's #1 Rental Platform</span>
          </div>
          <h1 class="text-4xl md:text-6xl font-black leading-tight mb-6">
            Find Your Perfect
            <span class="text-teal-400">Space</span> in Ethiopia
          </h1>
          <p class="text-lg md:text-xl text-navy-200 mb-10 max-w-xl">
            Discover apartments, houses, studios, and commercial spaces across
            Addis Ababa, Bahir Dar, Hawassa, and more — in Ethiopian Birr.
          </p>

          <!-- Search bar -->
          <div class="bg-white rounded-2xl p-2 flex flex-col sm:flex-row gap-2 shadow-2xl max-w-2xl">
            <div class="flex-1 relative">
              <SearchIcon :size="16" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
              <input
                v-model="heroSearch"
                type="text"
                placeholder="City, neighborhood, or keyword..."
                class="w-full pl-10 pr-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:outline-none rounded-xl"
              />
            </div>
            <select
              v-model="heroCity"
              class="text-sm text-gray-700 border-0 focus:outline-none px-3 py-3 bg-gray-50 rounded-xl min-w-[140px]"
            >
              <option value="">All Cities</option>
              <option v-for="c in CITIES" :key="c" :value="c">{{ c }}</option>
            </select>
            <RouterLink
              :to="searchRoute"
              class="btn btn-teal px-6 py-3 rounded-xl whitespace-nowrap"
            >
              <SearchIcon :size="16" />
              Search
            </RouterLink>
          </div>

          <!-- Stats -->
          <div class="flex flex-wrap gap-6 mt-10 text-sm text-navy-300">
            <span><span class="text-white font-bold text-lg">{{ stats.properties }}+</span> Properties</span>
            <span><span class="text-white font-bold text-lg">{{ stats.cities }}+</span> Cities</span>
            <span><span class="text-white font-bold text-lg">{{ stats.owners }}+</span> Verified Owners</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Browse by Type -->
    <section class="section bg-white">
      <div class="container-app">
        <div class="text-center mb-10">
          <h2 class="text-3xl font-bold text-navy-900">Browse by Type</h2>
          <p class="text-gray-500 mt-2">Find properties that match your needs</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
          <RouterLink
            v-for="type in PROPERTY_TYPES"
            :key="type.value"
            :to="{ name: 'properties', query: { type: type.value } }"
            class="card p-4 text-center hover:shadow-card-hover hover:border-navy-200 hover:-translate-y-1 transition-all duration-200 group"
          >
            <div class="w-12 h-12 bg-navy-50 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-navy-100 transition-colors">
              <component :is="getTypeIcon(type.value)" :size="22" class="text-navy-600" />
            </div>
            <p class="text-sm font-semibold text-gray-700">{{ type.label }}</p>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Featured Properties -->
    <section class="section bg-gray-50">
      <div class="container-app">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-3xl font-bold text-navy-900">Featured Properties</h2>
            <p class="text-gray-500 mt-1">Hand-picked listings across Ethiopia</p>
          </div>
          <RouterLink :to="{ name: 'properties' }" class="btn btn-secondary btn-sm hidden sm:flex">
            View All <ArrowRightIcon :size="14" />
          </RouterLink>
        </div>

        <!-- Loading -->
        <div v-if="propertyStore.loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="i in 6" :key="i" class="card overflow-hidden">
            <div class="skeleton aspect-[16/10]" />
            <div class="p-4 space-y-3">
              <div class="skeleton h-5 w-3/4" />
              <div class="skeleton h-4 w-1/2" />
              <div class="skeleton h-4 w-full" />
            </div>
          </div>
        </div>

        <!-- Cards -->
        <div v-else-if="propertyStore.featured.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <RouterLink
            v-for="p in propertyStore.featured"
            :key="p.id"
            :to="{ name: 'property.show', params: { id: p.id } }"
          >
            <PropertyCard
              :property="p"
              :show-favorite="auth.isAuthenticated"
              :is-favorited="favoriteIds.has(p.id)"
              @toggle-favorite="toggleFavorite"
            />
          </RouterLink>
        </div>

        <EmptyState v-else title="No featured properties" description="Check back soon for new listings." />
      </div>
    </section>

    <!-- How It Works -->
    <section class="section bg-white">
      <div class="container-app">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-navy-900">How RentSpace Works</h2>
          <p class="text-gray-500 mt-2">Simple steps to find or list your property</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="step in steps" :key="step.step" class="text-center">
            <div class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <component :is="step.icon" :size="24" class="text-teal-500" />
            </div>
            <div class="w-7 h-7 bg-navy-600 text-white text-xs font-bold rounded-full flex items-center justify-center mx-auto -mt-2 mb-3 relative z-10">
              {{ step.step }}
            </div>
            <h3 class="font-bold text-gray-900 mb-2">{{ step.title }}</h3>
            <p class="text-sm text-gray-500 leading-relaxed">{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Why RentSpace -->
    <section class="section bg-navy-900 text-white">
      <div class="container-app">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold">Why Choose RentSpace?</h2>
          <p class="text-navy-300 mt-2">Built for Ethiopia's rental market</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="feature in features" :key="feature.title" class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
            <div class="w-10 h-10 bg-teal-500/20 rounded-xl flex items-center justify-center mb-4">
              <component :is="feature.icon" :size="20" class="text-teal-400" />
            </div>
            <h3 class="font-semibold text-sm mb-1">{{ feature.title }}</h3>
            <p class="text-xs text-navy-300 leading-relaxed">{{ feature.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="section bg-teal-500 text-white">
      <div class="container-app text-center">
        <h2 class="text-3xl font-bold mb-3">Ready to Find Your Space?</h2>
        <p class="text-teal-100 mb-8 text-lg">Join thousands of tenants and owners on RentSpace</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <RouterLink :to="{ name: 'properties' }" class="btn bg-white text-teal-700 hover:bg-teal-50 btn-lg">
            <SearchIcon :size="18" /> Browse Properties
          </RouterLink>
          <RouterLink :to="{ name: 'register' }" class="btn bg-teal-600 border-2 border-white/30 text-white hover:bg-teal-700 btn-lg">
            List Your Property
          </RouterLink>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  Search        as SearchIcon,
  ArrowRight    as ArrowRightIcon,
  Home          as HomeIcon,
  Building2     as Building2Icon,
  LayoutDashboard as LayoutDashboardIcon,
  Building      as BuildingIcon,
  Store         as StoreIcon,
  Briefcase     as BriefcaseIcon,
  MessageSquare as MessageSquareIcon,
  Key           as KeyIcon,
  Shield        as ShieldIcon,
  CheckCircle2  as CheckCircle2Icon,
  Banknote      as BanknoteIcon,
  Users         as UsersIcon,
} from 'lucide-vue-next'

import PublicLayout   from '@/components/layout/PublicLayout.vue'
import PropertyCard   from '@/components/property/PropertyCard.vue'
import EmptyState     from '@/components/ui/EmptyState.vue'
import { usePropertyStore } from '@/stores/property.js'
import { useAuthStore }     from '@/stores/auth.js'
import favoriteService      from '@/services/favorite.js'
import { useUiStore }       from '@/stores/ui.js'
import { CITIES, PROPERTY_TYPES } from '@/utils/constants.js'

const propertyStore = usePropertyStore()
const auth          = useAuthStore()
const ui            = useUiStore()

const heroSearch = ref('')
const heroCity   = ref('')
const favoriteIds = ref(new Set())

const stats = ref({ properties: 500, cities: 13, owners: 200 })

const searchRoute = computed(() => ({
  name: 'properties',
  query: {
    ...(heroSearch.value && { search: heroSearch.value }),
    ...(heroCity.value && { city: heroCity.value }),
  },
}))

const ICON_MAP = {
  apartment:   Building2Icon,
  house:       HomeIcon,
  studio:      LayoutDashboardIcon,
  condominium: BuildingIcon,
  commercial:  StoreIcon,
  office:      BriefcaseIcon,
}

function getTypeIcon(type) { return ICON_MAP[type] ?? HomeIcon }

const steps = [
  { step: 1, icon: SearchIcon, title: 'Search Properties', desc: 'Browse hundreds of verified listings across Ethiopian cities with powerful filters.' },
  { step: 2, icon: MessageSquareIcon, title: 'Send Inquiry', desc: 'Found your space? Send an inquiry directly to the property owner.' },
  { step: 3, icon: KeyIcon, title: 'Move In', desc: 'Owner accepts your inquiry and you coordinate the move-in details.' },
]

const features = [
  { icon: ShieldIcon,       title: 'Verified Listings',     desc: 'All properties are reviewed by our team before going live.' },
  { icon: BanknoteIcon,     title: 'ETB Pricing',           desc: 'Prices displayed in Ethiopian Birr with no hidden fees.' },
  { icon: CheckCircle2Icon, title: 'Direct Communication',  desc: 'Message owners directly without intermediaries.' },
  { icon: UsersIcon,        title: 'Trusted Community',     desc: 'Thousands of verified owners and tenants across Ethiopia.' },
]

async function fetchFavorites() {
  if (!auth.isAuthenticated) return
  try {
    const { data } = await favoriteService.list({ per_page: 100 })
    favoriteIds.value = new Set(data.data.map(f => f.property?.id).filter(Boolean))
  } catch { /* silent — favorites are non-critical on homepage */ }
}

async function toggleFavorite(propertyId) {
  if (!auth.isAuthenticated) return
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
  } catch { /* silent */ }
}

onMounted(() => {
  propertyStore.fetchFeatured()
  fetchFavorites()
})
</script>
