<template>
  <PublicLayout>
    <!-- Loading -->
    <div v-if="loading" class="container-app py-12">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-4">
          <div class="skeleton aspect-video rounded-2xl" />
          <div class="skeleton h-8 w-3/4" />
          <div class="skeleton h-4 w-1/2" />
          <div class="skeleton h-32 w-full" />
        </div>
        <div class="space-y-4">
          <div class="skeleton h-48 rounded-2xl" />
          <div class="skeleton h-32 rounded-2xl" />
        </div>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="notFound" class="container-app py-20 text-center">
      <h2 class="text-2xl font-bold text-gray-700 mb-2">Property not found</h2>
      <p class="text-gray-500 mb-6">This listing may have been removed or is unavailable.</p>
      <RouterLink :to="{ name: 'properties' }" class="btn btn-primary">Browse Properties</RouterLink>
    </div>

    <!-- Content -->
    <template v-else-if="property">
      <!-- Image Gallery -->
      <div class="bg-gray-900 relative">
        <div class="container-app py-4">
          <!-- Main image -->
          <div class="relative aspect-video md:aspect-[21/9] rounded-2xl overflow-hidden bg-gray-800 cursor-pointer" @click="galleryOpen = true">
            <img
              v-if="currentImage"
              :src="currentImage"
              :alt="property.title"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center">
              <HomeIcon :size="60" class="text-gray-600" />
            </div>
            <!-- Nav arrows -->
            <button
              v-if="images.length > 1"
              class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 hover:bg-black/70 rounded-full flex items-center justify-center text-white transition-colors"
              @click.stop="prevImage"
            ><ChevronLeftIcon :size="20" /></button>
            <button
              v-if="images.length > 1"
              class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 hover:bg-black/70 rounded-full flex items-center justify-center text-white transition-colors"
              @click.stop="nextImage"
            ><ChevronRightIcon :size="20" /></button>
            <!-- Counter -->
            <div v-if="images.length > 1" class="absolute bottom-3 right-3 bg-black/50 text-white text-xs px-2.5 py-1 rounded-full">
              {{ currentIndex + 1 }} / {{ images.length }}
            </div>
            <!-- Expand icon -->
            <div class="absolute top-3 right-3 bg-black/50 text-white px-2.5 py-1 rounded-full text-xs flex items-center gap-1">
              <ImageIcon :size="12" /> {{ images.length }} photos
            </div>
          </div>

          <!-- Thumbnails -->
          <div v-if="images.length > 1" class="flex gap-2 mt-3 overflow-x-auto pb-1">
            <button
              v-for="(img, i) in images"
              :key="i"
              :class="['w-16 h-12 rounded-lg overflow-hidden shrink-0 ring-2 transition-all', i === currentIndex ? 'ring-teal-400' : 'ring-transparent opacity-60 hover:opacity-100']"
              @click="currentIndex = i"
            >
              <img :src="img" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="container-app py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

          <!-- Left: Details -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Header -->
            <div>
              <div class="flex items-start justify-between gap-4 mb-2">
                <h1 class="text-2xl md:text-3xl font-bold text-navy-900 leading-tight">{{ property.title }}</h1>
                <button
                  :class="['shrink-0 w-10 h-10 rounded-full border-2 flex items-center justify-center transition-all', isFavorited ? 'border-red-400 bg-red-50 text-red-500' : 'border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-400']"
                  @click="toggleFavorite"
                  :title="isFavorited ? 'Remove from favorites' : 'Save to favorites'"
                >
                  <HeartIcon :size="18" :fill="isFavorited ? 'currentColor' : 'none'" />
                </button>
              </div>

              <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-3">
                <span class="flex items-center gap-1.5"><MapPinIcon :size="14" /> {{ property.city }}<span v-if="property.sub_city">, {{ property.sub_city }}</span></span>
                <span class="text-gray-200">|</span>
                <BaseBadge :status="property.status">{{ capitalize(property.status) }}</BaseBadge>
                <span v-if="property.is_featured" class="badge bg-amber-100 text-amber-700">
                  <StarIcon :size="10" class="mr-1" /> Featured
                </span>
              </div>

              <div class="text-3xl font-black text-navy-700">
                {{ formatPricePerMonth(property.price) }}
              </div>
            </div>

            <!-- Spec cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
              <div v-for="spec in specs" :key="spec.label" class="card p-3.5 text-center">
                <component :is="spec.icon" :size="20" class="text-teal-500 mx-auto mb-1.5" />
                <p class="text-sm font-bold text-gray-900">{{ spec.value }}</p>
                <p class="text-xs text-gray-500">{{ spec.label }}</p>
              </div>
            </div>

            <!-- Description -->
            <div class="card p-5">
              <h2 class="font-semibold text-gray-900 mb-3">About this property</h2>
              <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ property.description }}</p>
            </div>

            <!-- Amenities -->
            <div v-if="property.amenities?.length" class="card p-5">
              <h2 class="font-semibold text-gray-900 mb-4">Amenities</h2>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-3 gap-x-4">
                <div v-for="amenity in property.amenities" :key="amenity.id" class="flex items-center gap-2 text-sm text-gray-700">
                  <div class="w-5 h-5 bg-teal-50 rounded-full flex items-center justify-center shrink-0">
                    <CheckIcon :size="10" class="text-teal-600" />
                  </div>
                  {{ amenity.name }}
                </div>
              </div>
            </div>

            <!-- Location details -->
            <div class="card p-5">
              <h2 class="font-semibold text-gray-900 mb-3">Location</h2>
              <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-center gap-2"><MapPinIcon :size="15" class="text-gray-400" /><span>{{ property.city }}<span v-if="property.sub_city">, {{ property.sub_city }}</span></span></div>
                <div v-if="property.address" class="flex items-center gap-2"><MapPinIcon :size="15" class="text-gray-400" /><span>{{ property.address }}</span></div>
                <div v-if="property.available_date" class="flex items-center gap-2"><CalendarIcon :size="15" class="text-gray-400" />Available from {{ formatDate(property.available_date) }}</div>
              </div>
            </div>
          </div>

          <!-- Right: Sidebar -->
          <div class="space-y-5">
            <!-- Owner card -->
            <div class="card p-5">
              <h2 class="font-semibold text-gray-900 mb-4">Listed by</h2>
              <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-navy-100 rounded-full flex items-center justify-center overflow-hidden shrink-0">
                  <img v-if="property.owner?.avatar" :src="property.owner.avatar" class="w-full h-full object-cover" />
                  <span v-else class="text-navy-700 font-bold text-lg">{{ ownerInitials }}</span>
                </div>
                <div>
                  <p class="font-semibold text-gray-900 text-sm">{{ property.owner?.name ?? 'Owner' }}</p>
                  <p class="text-xs text-gray-500">Property Owner</p>
                </div>
              </div>

              <!-- Action buttons -->
              <template v-if="auth.isTenant">
                <BaseButton class="w-full mb-2" :loading="inquiryLoading" @click="openInquiry">
                  <MessageSquareIcon :size="15" /> Send Inquiry
                </BaseButton>
              </template>
              <template v-else-if="!auth.isAuthenticated">
                <RouterLink :to="{ name: 'login' }" class="btn btn-primary w-full">
                  <MessageSquareIcon :size="15" /> Login to Inquire
                </RouterLink>
              </template>
              <div v-else-if="auth.isOwner && auth.user?.id === property.owner?.id" class="text-xs text-center text-gray-400 py-2">
                This is your listing
              </div>
            </div>

            <!-- Key Details card -->
            <div class="card p-5">
              <h2 class="font-semibold text-gray-900 mb-4">Key Details</h2>
              <dl class="space-y-3 text-sm">
                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                  <dt class="text-gray-500">Type</dt>
                  <dd class="font-medium text-gray-800 capitalize">{{ property.type }}</dd>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                  <dt class="text-gray-500">Furnishing</dt>
                  <dd class="font-medium text-gray-800 capitalize">{{ property.furnishing_status?.replace('-', ' ') }}</dd>
                </div>
                <div v-if="property.size_sqm" class="flex justify-between items-center border-b border-gray-50 pb-2">
                  <dt class="text-gray-500">Size</dt>
                  <dd class="font-medium text-gray-800">{{ property.size_sqm }} m²</dd>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                  <dt class="text-gray-500">Currency</dt>
                  <dd class="font-medium text-gray-800">{{ property.currency }}</dd>
                </div>
                <div class="flex justify-between items-center">
                  <dt class="text-gray-500">Listed</dt>
                  <dd class="font-medium text-gray-800">{{ timeAgo(property.created_at) }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>

        <!-- Similar Properties -->
        <div v-if="similar.length" class="mt-12">
          <h2 class="text-xl font-bold text-navy-900 mb-5">Similar Properties</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <RouterLink v-for="p in similar" :key="p.id" :to="{ name: 'property.show', params: { id: p.id } }">
              <PropertyCard :property="p" :show-favorite="auth.isAuthenticated" />
            </RouterLink>
          </div>
        </div>
      </div>
    </template>

    <!-- Inquiry Modal -->
    <BaseModal v-model="showInquiry" title="Send Inquiry" size="md">
      <div class="space-y-4">
        <p class="text-sm text-gray-600">
          Sending an inquiry for <strong>{{ property?.title }}</strong>
        </p>
        <BaseInput
          v-model="inquiry.message"
          label="Message"
          type="textarea"
          :rows="4"
          placeholder="Introduce yourself and explain your interest in this property..."
          :error="inquiryErrors.message"
          required
        />
        <BaseInput
          v-model="inquiry.preferred_date"
          label="Preferred viewing date"
          type="date"
          :error="inquiryErrors.preferred_date"
          :min="minDate"
        />
      </div>
      <template #footer>
        <div class="flex justify-end gap-3">
          <BaseButton variant="ghost" @click="showInquiry = false">Cancel</BaseButton>
          <BaseButton :loading="inquiryLoading" @click="submitInquiry">Send Inquiry</BaseButton>
        </div>
      </template>
    </BaseModal>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PublicLayout  from '@/components/layout/PublicLayout.vue'
import PropertyCard  from '@/components/property/PropertyCard.vue'
import BaseButton    from '@/components/ui/BaseButton.vue'
import BaseInput     from '@/components/ui/BaseInput.vue'
import BaseModal     from '@/components/ui/BaseModal.vue'
import BaseBadge     from '@/components/ui/BaseBadge.vue'
import {
  Home          as HomeIcon,
  Heart         as HeartIcon,
  MapPin        as MapPinIcon,
  BedDouble     as BedDoubleIcon,
  Bath          as BathIcon,
  UtensilsCrossed as KitchenIcon,
  Square        as SquareIcon,
  MessageSquare as MessageSquareIcon,
  ChevronLeft   as ChevronLeftIcon,
  ChevronRight  as ChevronRightIcon,
  Image         as ImageIcon,
  Check         as CheckIcon,
  Star          as StarIcon,
  Calendar      as CalendarIcon,
} from 'lucide-vue-next'
import propertyService  from '@/services/property.js'
import favoriteService  from '@/services/favorite.js'
import inquiryService   from '@/services/inquiry.js'
import { useAuthStore } from '@/stores/auth.js'
import { useUiStore }   from '@/stores/ui.js'
import { formatPricePerMonth, formatDate, timeAgo, capitalize } from '@/utils/format.js'

const route  = useRoute()
const router = useRouter()
const auth   = useAuthStore()
const ui     = useUiStore()

const property     = ref(null)
const loading      = ref(true)
const notFound     = ref(false)
const similar      = ref([])
const isFavorited  = ref(false)
const currentIndex = ref(0)
const galleryOpen  = ref(false)

const showInquiry   = ref(false)
const inquiryLoading= ref(false)
const inquiry       = ref({ message: '', preferred_date: '' })
const inquiryErrors = ref({ message: '', preferred_date: '' })

const minDate = computed(() => new Date().toISOString().split('T')[0])

const images = computed(() => property.value?.images?.map(i => i.url) ?? [])
const currentImage = computed(() => images.value[currentIndex.value] ?? null)

const ownerInitials = computed(() => {
  return (property.value?.owner?.name ?? 'O').split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
})

const specs = computed(() => {
  if (!property.value) return []
  return [
    { icon: BedDoubleIcon, label: 'Bedrooms',  value: property.value.bedrooms },
    { icon: BathIcon,      label: 'Bathrooms', value: property.value.bathrooms },
    { icon: KitchenIcon,   label: 'Kitchens',  value: property.value.kitchens },
    { icon: SquareIcon,    label: 'Size',       value: property.value.size_sqm ? property.value.size_sqm + 'm²' : 'N/A' },
  ]
})

function prevImage() { currentIndex.value = (currentIndex.value - 1 + images.value.length) % images.value.length }
function nextImage() { currentIndex.value = (currentIndex.value + 1) % images.value.length }

async function fetchProperty(id) {
  loading.value = true
  notFound.value = false
  try {
    const { data } = await propertyService.get(id)
    property.value = data.data
    // Check favorites
    if (auth.isAuthenticated) {
      const { data: favData } = await favoriteService.list({ per_page: 100 })
      isFavorited.value = favData.data.some(f => f.property?.id === property.value.id)
    }
    // Fetch similar
    fetchSimilar()
  } catch (err) {
    if (err.response?.status === 403 || err.response?.status === 404) notFound.value = true
    else ui.error('Could not load property.')
  } finally {
    loading.value = false
  }
}

async function fetchSimilar() {
  if (!property.value) return
  try {
    const { data } = await propertyService.list({
      type: property.value.type,
      city: property.value.city,
      per_page: 3,
    })
    similar.value = data.data.filter(p => p.id !== property.value.id).slice(0, 3)
  } catch { /* silent */ }
}

async function toggleFavorite() {
  if (!auth.isAuthenticated) { router.push({ name: 'login' }); return }
  try {
    const { data } = await favoriteService.toggle(property.value.id)
    isFavorited.value = data.favorited
    ui[data.favorited ? 'success' : 'info'](data.message)
  } catch { ui.error('Could not update favorites.') }
}

function openInquiry() {
  inquiry.value = { message: '', preferred_date: '' }
  inquiryErrors.value = { message: '', preferred_date: '' }
  showInquiry.value = true
}

async function submitInquiry() {
  inquiryErrors.value = { message: '', preferred_date: '' }
  inquiryLoading.value = true
  try {
    await inquiryService.store(property.value.id, inquiry.value)
    showInquiry.value = false
    ui.success('Inquiry sent! The owner will respond shortly.')
  } catch (err) {
    const data = err.response?.data
    if (data?.errors) {
      if (data.errors.message)        inquiryErrors.value.message = data.errors.message[0]
      if (data.errors.preferred_date) inquiryErrors.value.preferred_date = data.errors.preferred_date[0]
    } else {
      ui.error(data?.message ?? 'Could not send inquiry.')
    }
  } finally {
    inquiryLoading.value = false
  }
}

onMounted(() => fetchProperty(route.params.id))

watch(() => route.params.id, (id) => {
  if (id) {
    currentIndex.value = 0
    fetchProperty(id)
  }
})
</script>
