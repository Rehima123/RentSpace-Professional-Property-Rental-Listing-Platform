<template>
  <DashboardLayout :breadcrumbs="[{ label: 'My Properties', to: { name: 'owner.properties' } }, { label: 'Add Property' }]">
    <div class="max-w-3xl mx-auto">
      <div class="mb-6"><h1 class="page-title">Add New Property</h1><p class="page-subtitle">Fill in the details below to list your property</p></div>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Basic Info -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-5">Basic Information</h2>
          <div class="space-y-4">
            <BaseInput v-model="form.title" label="Property Title" placeholder="e.g. Modern 2-Bedroom Apartment in Bole" :error="errors.title" required />
            <BaseInput v-model="form.description" label="Description" type="textarea" :rows="5" placeholder="Describe your property in detail (min. 50 characters)..." :error="errors.description" required />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <BaseSelect v-model="form.type" label="Property Type" :options="PROPERTY_TYPES" :error="errors.type" required />
              <BaseSelect v-model="form.furnishing_status" label="Furnishing Status" :options="FURNISHING_STATUSES" :error="errors.furnishing_status" required />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <BaseInput v-model="form.price" label="Monthly Rent (ETB)" type="number" placeholder="e.g. 25000" :error="errors.price" required />
              <BaseInput v-model="form.size_sqm" label="Size (m²)" type="number" placeholder="e.g. 85" :error="errors.size_sqm" />
            </div>
          </div>
        </div>

        <!-- Location -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-5">Location</h2>
          <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <BaseSelect v-model="form.city" label="City" :options="cityOptions" :error="errors.city" required />
              <BaseInput v-model="form.sub_city" label="Sub-city / Area" placeholder="e.g. Bole, Sarbet" :error="errors.sub_city" />
            </div>
            <BaseInput v-model="form.address" label="Specific Address" placeholder="e.g. Bole Atlas, Near Edna Mall" :error="errors.address" />
            <BaseInput v-model="form.available_date" label="Available From" type="date" :min="today" :error="errors.available_date" />
          </div>
        </div>

        <!-- Rooms -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-5">Rooms</h2>
          <div class="grid grid-cols-3 gap-4">
            <BaseInput v-model="form.bedrooms" label="Bedrooms" type="number" min="0" max="20" :error="errors.bedrooms" required />
            <BaseInput v-model="form.bathrooms" label="Bathrooms" type="number" min="0" max="20" :error="errors.bathrooms" required />
            <BaseInput v-model="form.kitchens" label="Kitchens" type="number" min="0" max="10" :error="errors.kitchens" required />
          </div>
        </div>

        <!-- Amenities -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-4">Amenities</h2>
          <div v-if="allAmenities.length" class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
            <label
              v-for="a in allAmenities" :key="a.id"
              :class="['flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all text-sm', selectedAmenities.includes(a.id) ? 'border-teal-400 bg-teal-50 text-teal-700' : 'border-gray-200 hover:border-gray-300']"
            >
              <input type="checkbox" class="hidden" :value="a.id" v-model="selectedAmenities" />
              <div :class="['w-4 h-4 rounded border-2 flex items-center justify-center shrink-0', selectedAmenities.includes(a.id) ? 'bg-teal-500 border-teal-500' : 'border-gray-300']">
                <CheckIcon v-if="selectedAmenities.includes(a.id)" :size="10" class="text-white" />
              </div>
              {{ a.name }}
            </label>
          </div>
        </div>

        <!-- Images -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-2">Property Images</h2>
          <p class="text-xs text-gray-400 mb-4">Upload up to 10 images (JPEG, PNG, WebP — max 5MB each). First image will be the primary photo.</p>
          <input ref="fileInput" type="file" multiple accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden" @change="onFiles" />
          <div
            class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-navy-400 transition-colors"
            @click="fileInput?.click()"
            @dragover.prevent @drop.prevent="onDrop"
          >
            <ImageIcon :size="32" class="text-gray-300 mx-auto mb-2" />
            <p class="text-sm text-gray-500">Drag & drop images here or <span class="text-navy-600 font-medium">click to browse</span></p>
            <p class="text-xs text-gray-400 mt-1">{{ imagePreviews.length }}/10 images selected</p>
          </div>
          <div v-if="imagePreviews.length" class="grid grid-cols-3 sm:grid-cols-5 gap-2 mt-4">
            <div v-for="(preview, i) in imagePreviews" :key="i" class="relative group">
              <img :src="preview" class="aspect-square rounded-lg object-cover w-full" />
              <span v-if="i === 0" class="absolute top-1 left-1 bg-teal-500 text-white text-[10px] px-1.5 py-0.5 rounded font-medium">Primary</span>
              <button type="button" class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity" @click="removeImage(i)">
                <XIcon :size="10" />
              </button>
            </div>
          </div>
          <p v-if="errors.images" class="text-xs text-red-600 mt-2">{{ errors.images }}</p>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
          <RouterLink :to="{ name: 'owner.properties' }" class="btn btn-ghost">Cancel</RouterLink>
          <BaseButton type="submit" :loading="loading" size="lg">
            <PlusIcon :size="16" /> Submit Listing
          </BaseButton>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import DashboardLayout from '@/components/layout/DashboardLayout.vue'
import BaseInput  from '@/components/ui/BaseInput.vue'
import BaseSelect from '@/components/ui/BaseSelect.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import { Check as CheckIcon, X as XIcon, Image as ImageIcon, Plus as PlusIcon } from 'lucide-vue-next'
import propertyService from '@/services/property.js'
import { useUiStore }  from '@/stores/ui.js'
import { CITIES, PROPERTY_TYPES, FURNISHING_STATUSES } from '@/utils/constants.js'

const router = useRouter()
const ui     = useUiStore()

const form = reactive({
  title: '', description: '', type: '', price: '', size_sqm: '',
  city: '', sub_city: '', address: '', available_date: '',
  bedrooms: 0, bathrooms: 1, kitchens: 0, furnishing_status: '',
})
const errors           = reactive({})
const loading          = ref(false)
const allAmenities     = ref([])
const selectedAmenities= ref([])
const imageFiles       = ref([])
const imagePreviews    = ref([])
const fileInput        = ref(null)
const today            = new Date().toISOString().split('T')[0]

const cityOptions = computed(() => CITIES.map(c => ({ value: c, label: c })))

function onFiles(e) { addFiles(Array.from(e.target.files || [])) }
function onDrop(e)  { addFiles(Array.from(e.dataTransfer.files || [])) }

function addFiles(files) {
  const allowed = files.filter(f => f.type.startsWith('image/'))
  const remaining = 10 - imageFiles.value.length
  allowed.slice(0, remaining).forEach(f => {
    imageFiles.value.push(f)
    const reader = new FileReader()
    reader.onload = e => imagePreviews.value.push(e.target.result)
    reader.readAsDataURL(f)
  })
}

function removeImage(i) {
  imageFiles.value.splice(i, 1)
  imagePreviews.value.splice(i, 1)
}

async function handleSubmit() {
  Object.keys(errors).forEach(k => delete errors[k])
  loading.value = true
  try {
    const fd = new FormData()
    Object.entries(form).forEach(([k, v]) => { if (v !== '' && v !== null) fd.append(k, v) })
    selectedAmenities.value.forEach(id => fd.append('amenity_ids[]', id))
    imageFiles.value.forEach(f => fd.append('images[]', f))

    const { data } = await propertyService.store(fd)
    ui.success('Property submitted! It will be reviewed shortly.')
    router.push({ name: 'owner.properties' })
  } catch (err) {
    const data = err.response?.data
    if (data?.errors) Object.assign(errors, Object.fromEntries(Object.entries(data.errors).map(([k, v]) => [k, v[0]])))
    else ui.error(data?.message ?? 'Could not create property.')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  const { data } = await propertyService.amenities()
  allAmenities.value = data.data
})
</script>
