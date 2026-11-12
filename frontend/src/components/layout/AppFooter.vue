<template>
  <footer class="bg-navy-900 text-white">
    <div class="container-app py-12">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">

        <!-- Brand -->
        <div class="lg:col-span-2">
          <RouterLink :to="{ name: 'home' }" class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center">
              <HomeIcon :size="16" class="text-white" />
            </div>
            <span class="text-xl font-bold">Rent<span class="text-teal-400">Space</span></span>
          </RouterLink>
          <p class="text-sm text-navy-200 leading-relaxed max-w-sm">
            Ethiopia's trusted property rental platform. Find apartments, houses, studios,
            condominiums and commercial spaces across major Ethiopian cities.
          </p>
          <p class="mt-4 text-sm text-navy-300">
            <MapPinIcon :size="14" class="inline mr-1" />
            Addis Ababa, Ethiopia
          </p>
        </div>

        <!-- Quick links -->
        <div>
          <h4 class="font-semibold text-sm mb-4 text-navy-100">Quick Links</h4>
          <ul class="space-y-2.5">
            <li v-for="link in links" :key="link.name">
              <RouterLink :to="link.to" class="text-sm text-navy-300 hover:text-teal-400 transition-colors">
                {{ link.label }}
              </RouterLink>
            </li>
          </ul>
        </div>

        <!-- Cities -->
        <div>
          <h4 class="font-semibold text-sm mb-4 text-navy-100">Popular Cities</h4>
          <ul class="space-y-2.5">
            <li v-for="city in cities" :key="city">
              <RouterLink
                :to="{ name: 'properties', query: { city } }"
                class="text-sm text-navy-300 hover:text-teal-400 transition-colors"
              >
                {{ city }}
              </RouterLink>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom bar -->
      <div class="border-t border-navy-700 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-xs text-navy-400">
          © {{ new Date().getFullYear() }} RentSpace. All rights reserved.
        </p>
        <div class="flex items-center gap-6">
          <span class="text-xs text-navy-400">Currency: ETB (Ethiopian Birr)</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { Home as HomeIcon, MapPin as MapPinIcon } from 'lucide-vue-next'

const links = [
  { label: 'Browse Properties', to: { name: 'properties' } },
  { label: 'About Us',          to: { name: 'about' } },
  { label: 'Contact',           to: { name: 'contact' } },
  { label: 'Register',          to: { name: 'register' } },
]

const cities = ['Addis Ababa', 'Bahir Dar', 'Hawassa', 'Adama', 'Dessie']
</script>
