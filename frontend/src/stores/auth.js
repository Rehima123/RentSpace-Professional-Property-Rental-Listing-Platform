import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import authService from '@/services/auth.js'

export const useAuthStore = defineStore('auth', () => {
  const user        = ref(null)
  const loading     = ref(false)
  const initialized = ref(false)

  // ── Computed ─────────────────────────────────────────────────────────────
  const isAuthenticated = computed(() => !!user.value)
  const isAdmin    = computed(() => user.value?.role === 'admin')
  const isOwner    = computed(() => user.value?.role === 'owner')
  const isTenant   = computed(() => user.value?.role === 'tenant')
  const userRole   = computed(() => user.value?.role ?? null)
  const userName   = computed(() => user.value?.name ?? '')
  const userAvatar = computed(() => user.value?.avatar ?? null)

  // ── Actions ───────────────────────────────────────────────────────────────
  async function fetchUser() {
    try {
      const { data } = await authService.getUser()
      user.value = data.data
    } catch {
      user.value = null
    } finally {
      initialized.value = true
    }
  }

  async function register(payload) {
    loading.value = true
    try {
      const { data } = await authService.register(payload)
      user.value = data.data
      redirectAfterAuth()
    } finally {
      loading.value = false
    }
  }

  async function login(credentials) {
    loading.value = true
    try {
      const { data } = await authService.login(credentials)
      user.value = data.data
      redirectAfterAuth()
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    await authService.logout()
    user.value = null
    // useRouter() works in Pinia setup stores when called after the app mounts
    const router = useRouter()
    router.push({ name: 'home' })
  }

  function redirectAfterAuth() {
    const router = useRouter()
    const role = user.value?.role
    if (role === 'admin')       router.push({ name: 'admin.dashboard' })
    else if (role === 'owner')  router.push({ name: 'owner.dashboard' })
    else                        router.push({ name: 'tenant.dashboard' })
  }

  return {
    user, loading, initialized,
    isAuthenticated, isAdmin, isOwner, isTenant, userRole, userName, userAvatar,
    fetchUser, register, login, logout,
  }
})
