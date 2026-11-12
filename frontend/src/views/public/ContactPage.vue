<template>
  <PublicLayout>
    <!-- Header -->
    <div class="bg-navy-800 py-12">
      <div class="container-app">
        <h1 class="text-3xl font-bold text-white mb-1">Contact Us</h1>
        <p class="text-navy-300">Have a question? We're happy to help.</p>
      </div>
    </div>

    <div class="container-app py-12">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-5xl mx-auto">

        <!-- Contact Info -->
        <div>
          <h2 class="text-xl font-bold text-navy-900 mb-6">Get in touch</h2>
          <div class="space-y-5">
            <div v-for="item in contactInfo" :key="item.label" class="flex items-start gap-4">
              <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center shrink-0">
                <component :is="item.icon" :size="18" class="text-teal-600" />
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800">{{ item.label }}</p>
                <p class="text-sm text-gray-500 mt-0.5">{{ item.value }}</p>
              </div>
            </div>
          </div>

          <!-- Cities served -->
          <div class="mt-8 p-5 bg-navy-50 rounded-2xl">
            <p class="text-sm font-semibold text-navy-800 mb-3">Cities We Serve</p>
            <div class="flex flex-wrap gap-2">
              <span v-for="city in CITIES.slice(0, 8)" :key="city"
                class="text-xs px-2.5 py-1 bg-white text-navy-600 rounded-full border border-navy-100">
                {{ city }}
              </span>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="card p-6">
          <!-- Success state -->
          <div v-if="sent" class="flex flex-col items-center justify-center py-8 text-center">
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-4">
              <CheckCircle2Icon :size="28" class="text-green-600" />
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Message Sent!</h3>
            <p class="text-sm text-gray-500 mb-6">We'll get back to you as soon as possible.</p>
            <BaseButton variant="secondary" @click="sent = false">Send Another</BaseButton>
          </div>

          <!-- Form -->
          <form v-else @submit.prevent="handleSubmit" class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900 mb-5">Send a Message</h2>

            <BaseInput
              v-model="form.name"
              label="Full Name"
              placeholder="Your full name"
              :error="errors.name"
              required
            />
            <BaseInput
              v-model="form.email"
              label="Email Address"
              type="email"
              placeholder="you@example.com"
              :error="errors.email"
              required
            />
            <BaseInput
              v-model="form.subject"
              label="Subject"
              placeholder="What is your message about?"
              :error="errors.subject"
              required
            />
            <BaseInput
              v-model="form.message"
              label="Message"
              type="textarea"
              :rows="5"
              placeholder="Tell us how we can help..."
              :error="errors.message"
              required
            />

            <BaseButton type="submit" class="w-full" :loading="loading" size="lg">
              <SendIcon :size="16" /> Send Message
            </BaseButton>
          </form>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import PublicLayout from '@/components/layout/PublicLayout.vue'
import BaseInput  from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import {
  Mail         as MailIcon,
  Phone        as PhoneIcon,
  MapPin       as MapPinIcon,
  Send         as SendIcon,
  CheckCircle2 as CheckCircle2Icon,
} from 'lucide-vue-next'
import { CITIES } from '@/utils/constants.js'

const loading = ref(false)
const sent    = ref(false)
const form    = reactive({ name: '', email: '', subject: '', message: '' })
const errors  = reactive({ name: '', email: '', subject: '', message: '' })

const contactInfo = [
  { icon: MailIcon,   label: 'Email',    value: 'hello@rentspace.et' },
  { icon: PhoneIcon,  label: 'Phone',    value: '+251 911 000 000' },
  { icon: MapPinIcon, label: 'Location', value: 'Addis Ababa, Ethiopia' },
]

function validate() {
  let valid = true
  Object.keys(errors).forEach(k => errors[k] = '')
  if (!form.name.trim())    { errors.name    = 'Name is required.'; valid = false }
  if (!form.email.trim())   { errors.email   = 'Email is required.'; valid = false }
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) { errors.email = 'Enter a valid email.'; valid = false }
  if (!form.subject.trim()) { errors.subject  = 'Subject is required.'; valid = false }
  if (!form.message.trim()) { errors.message  = 'Message is required.'; valid = false }
  else if (form.message.trim().length < 20) { errors.message = 'Message must be at least 20 characters.'; valid = false }
  return valid
}

async function handleSubmit() {
  if (!validate()) return
  loading.value = true
  try {
    // Simulate a brief network delay — replace with real API call if a contact endpoint is added
    await new Promise(resolve => setTimeout(resolve, 800))
    sent.value = true
    Object.assign(form, { name: '', email: '', subject: '', message: '' })
  } finally {
    loading.value = false
  }
}
</script>
