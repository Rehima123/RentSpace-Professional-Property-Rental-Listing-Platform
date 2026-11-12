<template>
  <PublicLayout>
    <div class="container-app py-16">
      <h1 class="page-title mb-4">Contact Us</h1>
      <p class="text-gray-600 mb-8">Have a question or need help? Reach out to our team.</p>
      <div class="max-w-lg card p-6 space-y-4">
        <BaseInput v-model="form.name" label="Full Name" placeholder="Your name" required />
        <BaseInput v-model="form.email" label="Email" type="email" placeholder="your@email.com" required />
        <BaseInput v-model="form.message" label="Message" type="textarea" :rows="5" placeholder="Your message..." required />
        <BaseButton class="w-full" :loading="loading">Send Message</BaseButton>
      </div>
    </div>
  </PublicLayout>
</template>
<script setup>
import { ref } from 'vue'
import PublicLayout from '@/components/layout/PublicLayout.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
const form = ref({ name: '', email: '', message: '' })
const loading = ref(false)
</script>
