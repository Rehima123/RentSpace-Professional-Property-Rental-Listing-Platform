<template>
  <DashboardLayout :breadcrumbs="[{ label: 'My Properties', to: { name: 'owner.properties' } }, { label: 'Edit Property' }]">
    <div class="max-w-3xl mx-auto">
      <div class="mb-6"><h1 class="page-title">Edit Property</h1><p class="page-subtitle">Update your listing details</p></div>

      <BaseSpinner v-if="fetching" :full-page="true" />

      <form v-else @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Basic Info -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-5">Basic Information</h2>
          <div class="space-y-4">
            <BaseInput v-model="form.title" label="Title" :error="errors.title" required />
            <BaseInput v-model="form.description" label="Description" type="textarea" :rows="5" :error="errors.description" required />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <BaseSelect v-model="form.type" label="Type" :options="PROPERTY_TYPES" :error="errors.type" required />
              <BaseSelect v-model="form.furnishing_status" label="Furnishing" :options="FURNISHING_STATUSES" :error="errors.furnishing_status" required />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <BaseInput v-model="form.price" label="Price (ETB/month)" type="number" :error="errors.price" required />
              <BaseInput v-model="form.size_sqm" label="Size (m²)" type="number" :error="errors.size_sqm" />
            </div>
          </div>
        </div>

        <!-- Location -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-5">Location</h2>
          <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <BaseSelect v-model="form.city" label="City" :options="cityOptions" :error="errors.city" required />
              <BaseInput v-model="form.sub_city" label="Sub-city" :error="errors.sub_city" />
            </div>
            <BaseInput v-model="form.address" label="Address" :error="errors.address" />
            <BaseInput v-model="form.available_date" label="Available From" type="date" :error="errors.available_date" />
          </div>
        </div>

        <!-- Rooms -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-5">Rooms</h2>
          <div class="grid grid-cols-3 gap-4">
            <BaseInput v-model="form.bedrooms" label="Bedrooms" type="number" min="0" required />
            <BaseInput v-model="form.bathrooms" label="Bathrooms" type="number" min="0" required />
            <BaseInput v-model="form.kitchens" label="Kitchens" type="number" min="0" required />
          </div>
        </div>

        <!-- Status (owner can mark as rented) -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-4">Listing Status</h2>
          <BaseSelect v-model="form.status" label="Status" :options="ownerStatuses" />
          <p class="text-xs text-gray-400 mt-2">Note: Changing content will reset status to pending for re-approval.</p>
        </div>

        <!-- Amenities -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-4">Amenities</h2>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
            <label v-for="a in allAmenities" :key="a.id"
              :class="['flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer text-sm transition-all', selectedAmenities.includes(a.id) ? 'border-teal-400 bg-teal-50 text-teal-700' : 'border-gray-200 hover:border-gray-300']"
            >
              <input type="checkbox" class="hidden" :value="a.id" v-model="selectedAmenities" />
              <div :class="['w-4 h-4 rounded border-2 flex items-center justify-center shrink-0', selectedAmenities.includes(a.id) ? 'bg-teal-500 border-teal-500' : 'border-gray-300']">
                <CheckIcon v-if="selectedAmenities.includes(a.id)" :size="10" class="text-white" />
              </div>
              {{ a.name }}
            </label>
          </div>
        </div>

        <!-- Existing Images -->
        <div v-if="existingImages.length" class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-4">Existing Images</h2>
          <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
            <div v-for="img in existingImages" :key="img.id" class="relative group">
              <img :src="img.url" class="aspect-square rounded-lg object-cover w-full" />
              <span v-if="img.is_primary" class="absolute top-1 left-1 bg-teal-500 text-white text-[10px] px-1.5 py-0.5 rounded font-medium">Primary</span>
              <button type="button" class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100" @click="deleteImage(img)">
                <XIcon :size="10" />
              </button>
            </div>
          </div>
        </div>

        <!-- Add New Images -->
        <div class="card p-6">
          <h2 class="font-semibold text-gray-900 mb-2">Add More Images</h2>
          <input ref="fileInput" type="file" multiple accept="image/*" class="hidden" @change="onFiles" />
          <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-navy-400" @click="fileInput?.click()">
            <p class="text-sm text-gray-500">Click to add more images</p>
          </div>
          <div v-if="imagePreviews.length" class="grid grid-cols-5 gap-2 mt-3">
            <div v-for="(p, i) in imagePreviews" :key="i" class="relative group">
              <img :src="p" class="aspect-square rounded-lg object-cover w-full" />
              <button type="button" class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100" @click="removeNew(i)">
                <XIcon :size="10" />
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3">
          <RouterLink :to="{ name: 'owner.properties' }" class="btn btn-ghost">Cancel</RouterLink>
          <BaseButton type="submit" :loading="loading">Save Changes</BaseButton>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import DashboardLayout from '@/components/layout/DashboardLayout.vue'
import BaseInput   from '@/components/ui/BaseInput.vue'
import BaseSelect  from '@/components/ui/BaseSelect.vue'
import BaseButton  from '@/components/ui/BaseButton.vue'
import BaseSpinner from '@/components/ui/BaseSpinner.vue'
import { Check as CheckIcon, X as XIcon } from 'lucide-vue-next'
import propertyService from '@/services/property.js'
import { useUiStore }  from '@/stores/ui.js'
import { CITIES, PROPERTY_TYPES, FURNISHING_STATUSES } from '@/utils/constants.js'

const route  = useRoute()
const router = useRouter()
const ui     = useUiStore()

const form = reactive({ title:'',description:'',type:'',price:'',size_sqm:'',city:'',sub_city:'',address:'',available_date:'',bedrooms:0,bathrooms:1,kitchens:0,furnishing_status:'',status:'' })
const errors            = reactive({})
const loading           = ref(false)
const fetching          = ref(true)
const allAmenities      = ref([])
const selectedAmenities = ref([])
const existingImages    = ref([])
const imageFiles        = ref([])
const imagePreviews     = ref([])
const fileInput         = ref(null)

const cityOptions   = computed(() => CITIES.map(c => ({ value: c, label: c })))
const ownerStatuses = [{ value: 'rented', label: 'Rented' }, { value: 'unavailable', label: 'Unavailable' }, { value: 'pending', label: 'Re-submit for Approval' }]

function onFiles(e) {
  Array.from(e.target.files || []).forEach(f => {
    imageFiles.value.push(f)
    const r = new FileReader()
    r.onload = e => imagePreviews.value.push(e.target.result)
    r.readAsDataURL(f)
  })
}
function removeNew(i) { imageFiles.value.splice(i,1); imagePreviews.value.splice(i,1) }

async function deleteImage(img) {
  try {
    await propertyService.destroyImage(route.params.id, img.id)
    existingImages.value = existingImages.value.filter(x => x.id !== img.id)
    ui.info('Image removed.')
  } catch { ui.error('Could not remove image.') }
}

async function handleSubmit() {
  Object.keys(errors).forEach(k => delete errors[k])
  loading.value = true
  try {
    const fd = new FormData()
    Object.entries(form).forEach(([k,v]) => { if (v !== '' && v !== null) fd.append(k, v) })
    selectedAmenities.value.forEach(id => fd.append('amenity_ids[]', id))
    imageFiles.value.forEach(f => fd.append('images[]', f))
    await propertyService.update(route.params.id, fd)
    ui.success('Property updated!')
    router.push({ name: 'owner.properties' })
  } catch (err) {
    const data = err.response?.data
    if (data?.errors) Object.assign(errors, Object.fromEntries(Object.entries(data.errors).map(([k,v]) => [k,v[0]])))
    else ui.error(data?.message ?? 'Could not update.')
  } finally { loading.value = false }
}

onMounted(async () => {
  const [propRes, amenRes] = await Promise.all([propertyService.get(route.params.id), propertyService.amenities()])
  const p = propRes.data.data
  Object.assign(form, { title:p.title, description:p.description, type:p.type, price:p.price, size_sqm:p.size_sqm??'', city:p.city, sub_city:p.sub_city??'', address:p.address??'', available_date:p.available_date??'', bedrooms:p.bedrooms, bathrooms:p.bathrooms, kitchens:p.kitchens, furnishing_status:p.furnishing_status, status:p.status })
  existingImages.value    = p.images ?? []
  selectedAmenities.value = p.amenities?.map(a => a.id) ?? []
  allAmenities.value      = amenRes.data.data
  fetching.value          = false
})
</script>
