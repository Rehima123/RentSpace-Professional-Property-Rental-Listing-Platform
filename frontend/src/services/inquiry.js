import api from './api.js'

export default {
  // Tenant
  store(propertyId, payload) {
    return api.post(`/api/properties/${propertyId}/inquiries`, payload)
  },

  tenantList(params = {}) {
    return api.get('/api/tenant/inquiries', { params })
  },

  // Owner
  ownerList(params = {}) {
    return api.get('/api/owner/inquiries', { params })
  },

  updateStatus(id, status) {
    return api.patch(`/api/owner/inquiries/${id}/status`, { status })
  },
}
