import { defineStore } from 'pinia'
import { ref } from 'vue'
import propertyService from '@/services/property.js'

export const usePropertyStore = defineStore('property', () => {
  const properties    = ref([])
  const featured      = ref([])
  const current       = ref(null)
  const amenities     = ref([])
  const cities        = ref([])
  const pagination    = ref(null)
  const loading       = ref(false)
  const filters       = ref({
    search: '',
    city: '',
    type: '',
    min_price: '',
    max_price: '',
    bedrooms: '',
    bathrooms: '',
    furnishing_status: '',
    sort: 'newest',
    per_page: 12,
  })

  async function fetchProperties(params = {}) {
    loading.value = true
    try {
      const merged = { ...filters.value, ...params }
      // Remove empty values
      const clean = Object.fromEntries(Object.entries(merged).filter(([, v]) => v !== '' && v !== null))
      const { data } = await propertyService.list(clean)
      properties.value = data.data
      pagination.value = data.meta
    } finally {
      loading.value = false
    }
  }

  async function fetchFeatured() {
    const { data } = await propertyService.featured()
    featured.value = data.data
  }

  async function fetchProperty(id) {
    loading.value = true
    try {
      const { data } = await propertyService.get(id)
      current.value = data.data
    } finally {
      loading.value = false
    }
  }

  async function fetchAmenities() {
    if (amenities.value.length) return
    const { data } = await propertyService.amenities()
    amenities.value = data.data
  }

  async function fetchCities() {
    const { data } = await propertyService.cities()
    cities.value = data.data
  }

  function setFilter(key, value) {
    filters.value[key] = value
  }

  function clearFilters() {
    filters.value = {
      search: '',
      city: '',
      type: '',
      min_price: '',
      max_price: '',
      bedrooms: '',
      bathrooms: '',
      furnishing_status: '',
      sort: 'newest',
      per_page: 12,
    }
  }

  return {
    properties, featured, current, amenities, cities, pagination, loading, filters,
    fetchProperties, fetchFeatured, fetchProperty, fetchAmenities, fetchCities,
    setFilter, clearFilters,
  }
})
