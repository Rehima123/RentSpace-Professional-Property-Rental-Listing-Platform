import api from './api.js'

export default {
  list(params = {}) {
    return api.get('/api/favorites', { params })
  },

  toggle(propertyId) {
    return api.post(`/api/properties/${propertyId}/favorite`)
  },
}
