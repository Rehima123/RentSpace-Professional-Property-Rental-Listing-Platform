<template>
  <header class="sticky top-0 z-40 bg-white border-b border-gray-100 shadow-sm">
    <div class="container-app">
      <div class="flex items-center justify-between h-16">

        <!-- Brand -->
        <RouterLink :to="{ name: 'home' }" class="flex items-center gap-2 group">
          <div class="w-8 h-8 bg-navy-600 rounded-lg flex items-center justify-center">
            <HomeIcon :size="16" class="text-white" />
          </div>
          <span class="text-xl font-bold text-navy-900 group-hover:text-navy-700 transition-colors">
            Rent<span class="text-teal-500">Space</span>
          </span>
        </RouterLink>

        <!-- Desktop nav -->
        <nav class="hidden md:flex items-center gap-6">
          <RouterLink
            :to="{ name: 'properties' }"
            class="text-sm font-medium text-gray-600 hover:text-navy-600 transition-colors"
            :class="{ 'text-navy-600': route.name === 'properties' }"
          >
            Properties
          </RouterLink>
          <RouterLink
            :to="{ name: 'about' }"
            class="text-sm font-medium text-gray-600 hover:text-navy-600 transition-colors"
          >
            About
          </RouterLink>
          <RouterLink
            :to="{ name: 'contact' }"
            class="text-sm font-medium text-gray-600 hover:text-navy-600 transition-colors"
          >
            Contact
          </RouterLink>
        </nav>

        <!-- Auth area -->
        <div class="flex items-center gap-3">
          <!-- Not logged in -->
          <template v-if="!auth.isAuthenticated">
            <RouterLink :to="{ name: 'login' }" class="btn btn-ghost btn-sm text-gray-700">
              Login
            </RouterLink>
            <RouterLink :to="{ name: 'register' }" class="btn btn-primary btn-sm">
              Get Started
            </RouterLink>
          </template>

          <!-- Logged in -->
          <template v-else>
            <!-- Dashboard shortcut -->
            <RouterLink :to="dashboardRoute" class="hidden md:block btn btn-ghost btn-sm text-gray-600">
              <LayoutDashboardIcon :size="15" />
              <span>Dashboard</span>
            </RouterLink>

            <!-- User menu -->
            <div class="relative" ref="menuRef">
              <button
                class="flex items-center gap-2 rounded-full border-2 border-gray-200 hover:border-navy-300 transition-colors p-0.5 pr-2"
                @click="menuOpen = !menuOpen"
              >
                <div class="w-7 h-7 rounded-full bg-navy-600 flex items-center justify-center overflow-hidden">
                  <img v-if="auth.userAvatar" :src="auth.userAvatar" class="w-full h-full object-cover" alt="avatar" />
                  <span v-else class="text-white text-xs font-semibold">{{ initials }}</span>
                </div>
                <span class="text-sm font-medium text-gray-700 max-w-[80px] truncate hidden sm:block">{{ auth.userName }}</span>
                <ChevronDownIcon :size="13" class="text-gray-400" :class="{ 'rotate-180': menuOpen }" />
              </button>

              <!-- Dropdown -->
              <Transition name="slide-up">
                <div
                  v-if="menuOpen"
                  class="absolute right-0 top-full mt-2 w-52 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden"
                >
                  <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth.userName }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ auth.userRole }}</p>
                  </div>
                  <div class="py-1">
                    <RouterLink
                      v-for="item in menuItems"
                      :key="item.name"
                      :to="item.to"
                      class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                      @click="menuOpen = false"
                    >
                      <component :is="item.icon" :size="15" class="text-gray-400" />
                      {{ item.label }}
                    </RouterLink>
                  </div>
                  <div class="border-t border-gray-100 py-1">
                    <button
                      class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors"
                      @click="handleLogout"
                    >
                      <LogOutIcon :size="15" />
                      Sign out
                    </button>
                  </div>
                </div>
              </Transition>
            </div>
          </template>

          <!-- Mobile hamburger -->
          <button class="md:hidden btn btn-ghost btn-sm" @click="mobileOpen = !mobileOpen">
            <MenuIcon :size="20" />
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile nav drawer -->
    <Transition name="slide-up">
      <div v-if="mobileOpen" class="md:hidden border-t border-gray-100 bg-white px-4 pb-4 space-y-1">
        <RouterLink
          v-for="link in mobileLinks"
          :key="link.name"
          :to="link.to"
          class="block py-2.5 text-sm font-medium text-gray-700 hover:text-navy-600"
          @click="mobileOpen = false"
        >
          {{ link.label }}
        </RouterLink>
      </div>
    </Transition>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import {
  Home            as HomeIcon,
  LayoutDashboard as LayoutDashboardIcon,
  ChevronDown     as ChevronDownIcon,
  Menu            as MenuIcon,
  LogOut          as LogOutIcon,
  User            as UserIcon,
  Heart           as HeartIcon,
  MessageSquare   as MessageSquareIcon,
  Settings        as SettingsIcon,
} from 'lucide-vue-next'

const auth  = useAuthStore()
const route = useRoute()

const menuOpen   = ref(false)
const mobileOpen = ref(false)
const menuRef    = ref(null)

const initials = computed(() => {
  return auth.userName.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
})

const dashboardRoute = computed(() => {
  if (auth.isAdmin)  return { name: 'admin.dashboard' }
  if (auth.isOwner)  return { name: 'owner.dashboard' }
  return { name: 'tenant.dashboard' }
})

const menuItems = computed(() => {
  const common = [{ label: 'Profile', to: { name: 'profile' }, icon: UserIcon }]
  if (auth.isTenant) return [
    ...common,
    { label: 'Favorites',  to: { name: 'tenant.favorites' },  icon: HeartIcon },
    { label: 'Inquiries',  to: { name: 'tenant.inquiries' },  icon: MessageSquareIcon },
  ]
  if (auth.isOwner) return [
    ...common,
    { label: 'My Properties', to: { name: 'owner.properties' }, icon: SettingsIcon },
    { label: 'Inquiries',     to: { name: 'owner.inquiries' },  icon: MessageSquareIcon },
  ]
  return common
})

const mobileLinks = [
  { label: 'Properties', to: { name: 'properties' } },
  { label: 'About',      to: { name: 'about' } },
  { label: 'Contact',    to: { name: 'contact' } },
  { label: 'Login',      to: { name: 'login' } },
  { label: 'Register',   to: { name: 'register' } },
]

async function handleLogout() {
  menuOpen.value = false
  await auth.logout()
}

function handleClickOutside(e) {
  if (menuRef.value && !menuRef.value.contains(e.target)) {
    menuOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>
