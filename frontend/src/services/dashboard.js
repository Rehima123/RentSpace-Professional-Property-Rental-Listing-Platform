import api from './api.js'

export default {
  owner()  { return api.get('/api/owner/dashboard') },
  tenant() { return api.get('/api/tenant/dashboard') },
  admin()  { return api.get('/api/admin/dashboard') },

  // Admin - user management
  adminUsers(params = {}) {
    return api.get('/api/admin/users', { params })
  },

  adminUpdateUserStatus(userId, status) {
    return api.patch(`/api/admin/users/${userId}/status`, { status })
  },

  // Admin - property management
  adminProperties(params = {}) {
    return api.get('/api/properties', { params })
  },
}
