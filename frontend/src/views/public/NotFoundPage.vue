<template>
  <PublicLayout>
    <div class="flex flex-col items-center justify-center min-h-[70vh] text-center px-4">
      <h1 class="text-8xl font-black text-navy-200 mb-4">404</h1>
      <h2 class="text-2xl font-bold text-navy-800 mb-2">Page not found</h2>
      <p class="text-gray-500 mb-8 max-w-sm">The page you are looking for does not exist or has been moved.</p>
      <RouterLink :to="{ name: 'home' }" class="btn btn-primary btn-lg">Back to Home</RouterLink>
    </div>
  </PublicLayout>
</template>
<script setup>
import PublicLayout from '@/components/layout/PublicLayout.vue'
</script>
