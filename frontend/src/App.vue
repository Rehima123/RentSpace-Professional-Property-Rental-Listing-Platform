<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <RouterView />

    <!-- Global Toast Notifications -->
    <ToastContainer />

    <!-- Global Confirm Dialog -->
    <ConfirmDialog />
  </div>
</template>

<script setup>
import ToastContainer from '@/components/ui/ToastContainer.vue'
import ConfirmDialog  from '@/components/ui/ConfirmDialog.vue'
</script>
