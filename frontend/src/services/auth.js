import api from './api.js'

/**
 * Fetch the CSRF cookie from Sanctum before any state-mutating request.
 * Must be called before login and register.
 */
async function getCsrfCookie() {
  await api.get('/sanctum/csrf-cookie')
}

export default {
  async register(payload) {
    await getCsrfCookie()
    return api.post('/api/auth/register', payload)
  },

  async login(credentials) {
    await getCsrfCookie()
    return api.post('/api/auth/login', credentials)
  },

  async logout() {
    return api.post('/api/auth/logout')
  },

  async getUser() {
    return api.get('/api/user')
  },

  async getProfile() {
    return api.get('/api/profile')
  },

  async updateProfile(payload) {
    // Profile updates may include a file (avatar); send as FormData if needed
    const isMultipart = payload instanceof FormData
    return api.put('/api/profile', payload, {
      headers: isMultipart ? { 'Content-Type': 'multipart/form-data' } : {},
    })
  },
}
