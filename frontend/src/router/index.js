import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'

const routes = [
  // ── Public ────────────────────────────────────────────────────────────────
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/public/HomePage.vue'),
    meta: { title: 'RentSpace — Find Your Perfect Space' },
  },
  {
    path: '/properties',
    name: 'properties',
    component: () => import('@/views/public/PropertiesPage.vue'),
    meta: { title: 'Browse Properties — RentSpace' },
  },
  {
    path: '/properties/:id',
    name: 'property.show',
    component: () => import('@/views/public/PropertyDetailsPage.vue'),
    meta: { title: 'Property Details — RentSpace' },
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('@/views/public/AboutPage.vue'),
    meta: { title: 'About Us — RentSpace' },
  },
  {
    path: '/contact',
    name: 'contact',
    component: () => import('@/views/public/ContactPage.vue'),
    meta: { title: 'Contact — RentSpace' },
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/public/LoginPage.vue'),
    meta: { title: 'Login — RentSpace', guest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/public/RegisterPage.vue'),
    meta: { title: 'Register — RentSpace', guest: true },
  },

  // ── Tenant ────────────────────────────────────────────────────────────────
  {
    path: '/dashboard',
    name: 'tenant.dashboard',
    component: () => import('@/views/tenant/TenantDashboard.vue'),
    meta: { requiresAuth: true, role: 'tenant', title: 'Dashboard — RentSpace' },
  },
  {
    path: '/favorites',
    name: 'tenant.favorites',
    component: () => import('@/views/tenant/FavoritesPage.vue'),
    meta: { requiresAuth: true, role: 'tenant', title: 'My Favorites — RentSpace' },
  },
  {
    path: '/inquiries',
    name: 'tenant.inquiries',
    component: () => import('@/views/tenant/MyInquiriesPage.vue'),
    meta: { requiresAuth: true, role: 'tenant', title: 'My Inquiries — RentSpace' },
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('@/views/tenant/ProfilePage.vue'),
    meta: { requiresAuth: true, title: 'Profile — RentSpace' },
  },

  // ── Owner ─────────────────────────────────────────────────────────────────
  {
    path: '/owner/dashboard',
    name: 'owner.dashboard',
    component: () => import('@/views/owner/OwnerDashboard.vue'),
    meta: { requiresAuth: true, role: 'owner', title: 'Owner Dashboard — RentSpace' },
  },
  {
    path: '/owner/properties',
    name: 'owner.properties',
    component: () => import('@/views/owner/ManagePropertiesPage.vue'),
    meta: { requiresAuth: true, role: 'owner', title: 'My Properties — RentSpace' },
  },
  {
    path: '/owner/properties/new',
    name: 'owner.properties.create',
    component: () => import('@/views/owner/AddPropertyPage.vue'),
    meta: { requiresAuth: true, role: 'owner', title: 'Add Property — RentSpace' },
  },
  {
    path: '/owner/properties/:id/edit',
    name: 'owner.properties.edit',
    component: () => import('@/views/owner/EditPropertyPage.vue'),
    meta: { requiresAuth: true, role: 'owner', title: 'Edit Property — RentSpace' },
  },
  {
    path: '/owner/inquiries',
    name: 'owner.inquiries',
    component: () => import('@/views/owner/ManageInquiriesPage.vue'),
    meta: { requiresAuth: true, role: 'owner', title: 'Manage Inquiries — RentSpace' },
  },

  // ── Admin ─────────────────────────────────────────────────────────────────
  {
    path: '/admin',
    name: 'admin.dashboard',
    component: () => import('@/views/admin/AdminDashboard.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Admin Dashboard — RentSpace' },
  },
  {
    path: '/admin/users',
    name: 'admin.users',
    component: () => import('@/views/admin/ManageUsersPage.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Manage Users — RentSpace' },
  },
  {
    path: '/admin/listings',
    name: 'admin.listings',
    component: () => import('@/views/admin/ManageListingsPage.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Manage Listings — RentSpace' },
  },

  // ── 404 ───────────────────────────────────────────────────────────────────
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/public/NotFoundPage.vue'),
    meta: { title: '404 — RentSpace' },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    return { top: 0, behavior: 'smooth' }
  },
})

// ── Navigation Guards ─────────────────────────────────────────────────────
router.beforeEach(async (to) => {
  // Update page title
  document.title = to.meta.title ?? 'RentSpace'

  const auth = useAuthStore()

  // Initialize auth state once per session
  if (!auth.initialized) {
    await auth.fetchUser()
  }

  // Guest-only routes (login, register)
  if (to.meta.guest && auth.isAuthenticated) {
    return { name: auth.isAdmin ? 'admin.dashboard' : auth.isOwner ? 'owner.dashboard' : 'tenant.dashboard' }
  }

  // Protected routes
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  // Role-specific routes
  if (to.meta.role && auth.isAuthenticated && auth.userRole !== to.meta.role && !auth.isAdmin) {
    // Admins can access any route; others can't cross roles
    return { name: auth.isOwner ? 'owner.dashboard' : 'tenant.dashboard' }
  }
})

export default router
