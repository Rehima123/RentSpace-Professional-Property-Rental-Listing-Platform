<template>
  <div class="flex flex-col min-h-screen bg-gray-50">
    <AppNavbar />
    <div class="flex-1 container-app py-8">
      <!-- Breadcrumb -->
      <nav v-if="breadcrumbs?.length" class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <RouterLink :to="{ name: 'home' }" class="hover:text-navy-600 transition-colors">Home</RouterLink>
        <span v-for="crumb in breadcrumbs" :key="crumb.label" class="flex items-center gap-2">
          <span>/</span>
          <RouterLink v-if="crumb.to" :to="crumb.to" class="hover:text-navy-600 transition-colors">
            {{ crumb.label }}
          </RouterLink>
          <span v-else class="text-gray-900 font-medium">{{ crumb.label }}</span>
        </span>
      </nav>

      <slot />
    </div>
    <AppFooter />
  </div>
</template>

<script setup>
import AppNavbar from './AppNavbar.vue'
import AppFooter from './AppFooter.vue'

defineProps({
  breadcrumbs: { type: Array, default: null },
})
</script>
