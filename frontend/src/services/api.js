import axios from 'axios'

/**
 * Central Axios instance for Sanctum SPA authentication.
 *
 * Key settings:
 *  - baseURL:        points to the Laravel backend
 *  - withCredentials: sends cookies (required for Sanctum SPA)
 *  - withXSRFToken:  auto-attaches the XSRF-TOKEN cookie as X-XSRF-TOKEN header
 *  - Accept: application/json ensures Laravel returns JSON errors, not HTML
 */
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000',
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    'Accept':       'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

/**
 * Response interceptor: surfaces error messages from Laravel's JSON responses
 * and handles 401 / 403 globally.
 */
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // The auth store handles redirect on 401 — just re-throw so callers can react
    }
    return Promise.reject(error)
  }
)

export default api
