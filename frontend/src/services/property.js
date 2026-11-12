import api from './api.js'

export default {
  // ── Public ────────────────────────────────────────────
  list(params = {}) {
    return api.get('/api/properties', { params })
  },

  featured() {
    return api.get('/api/properties/featured')
  },

  cities() {
    return api.get('/api/properties/cities')
  },

  get(idOrSlug) {
    return api.get(`/api/properties/${idOrSlug}`)
  },

  amenities() {
    return api.get('/api/amenities')
  },

  // ── Owner ─────────────────────────────────────────────
  ownerList(params = {}) {
    return api.get('/api/owner/properties', { params })
  },

  store(formData) {
    return api.post('/api/owner/properties', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  update(id, formData) {
    // FormData doesn't support PUT natively in some environments; use POST + _method
    if (formData instanceof FormData) {
      formData.append('_method', 'PUT')
      return api.post(`/api/owner/properties/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    }
    return api.put(`/api/owner/properties/${id}`, formData)
  },

  destroy(id) {
    return api.delete(`/api/owner/properties/${id}`)
  },

  destroyImage(propertyId, imageId) {
    return api.delete(`/api/owner/properties/${propertyId}/images/${imageId}`)
  },

  // ── Admin ─────────────────────────────────────────────
  updateStatus(id, status) {
    return api.patch(`/api/admin/properties/${id}/status`, { status })
  },
}
