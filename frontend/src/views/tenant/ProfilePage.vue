<template>
  <DashboardLayout :breadcrumbs="[{ label: 'Profile' }]">
    <div class="max-w-2xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="page-title">My Profile</h1>
          <p class="page-subtitle">Manage your personal information</p>
        </div>
      </div>

      <div class="card p-6">
        <!-- Avatar -->
        <div class="flex items-center gap-5 mb-8 pb-6 border-b border-gray-100">
          <div class="relative">
            <div class="w-20 h-20 rounded-full bg-navy-100 flex items-center justify-center overflow-hidden">
              <img v-if="auth.userAvatar" :src="auth.userAvatar" class="w-full h-full object-cover" />
              <span v-else class="text-2xl font-bold text-navy-600">{{ initials }}</span>
            </div>
          </div>
          <div>
            <p class="font-bold text-gray-900 text-lg">{{ auth.userName }}</p>
            <p class="text-sm text-gray-500 capitalize">{{ auth.userRole }}</p>
            <p class="text-sm text-teal-600 mt-0.5">{{ auth.user?.email }}</p>
          </div>
        </div>

        <!-- Success banner -->
        <div v-if="saved" class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4">
          <CheckCircle2Icon :size="16" class="text-green-500 shrink-0" />
          <p class="text-sm text-green-700 font-medium">Profile updated successfully!</p>
        </div>

        <form @submit.prevent="handleSave" class="space-y-5">
          <BaseInput
            v-model="form.name"
            label="Full name"
            placeholder="Your full name"
            :error="errors.name"
            required
          />
          <BaseInput
            v-model="form.email"
            label="Email address"
            type="email"
            :error="errors.email"
            required
          />
          <BaseInput
            v-model="form.phone"
            label="Phone number"
            placeholder="+251 9XX XXX XXX"
            :error="errors.phone"
          />

          <div class="border-t border-gray-100 pt-5">
            <p class="text-sm font-semibold text-gray-700 mb-4">Change Password <span class="font-normal text-gray-400">(leave blank to keep current)</span></p>
            <div class="space-y-4">
              <BaseInput
                v-model="form.password"
                label="New password"
                type="password"
                placeholder="Min. 8 characters"
                :error="errors.password"
              />
              <BaseInput
                v-model="form.password_confirmation"
                label="Confirm new password"
                type="password"
                placeholder="Repeat new password"
              />
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <BaseButton type="submit" :loading="loading">Save Changes</BaseButton>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import DashboardLayout from '@/components/layout/DashboardLayout.vue'
import BaseInput  from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import { CheckCircle2 as CheckCircle2Icon } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth.js'
import { useUiStore }   from '@/stores/ui.js'
import authService      from '@/services/auth.js'

const auth = useAuthStore()
const ui   = useUiStore()

const loading = ref(false)
const saved   = ref(false)
const form    = reactive({ name: '', email: '', phone: '', password: '', password_confirmation: '' })
const errors  = reactive({ name: '', email: '', phone: '', password: '' })

const initials = computed(() => auth.userName.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase())

onMounted(() => {
  form.name  = auth.user?.name ?? ''
  form.email = auth.user?.email ?? ''
  form.phone = auth.user?.phone ?? ''
})

async function handleSave() {
  Object.keys(errors).forEach(k => errors[k] = '')
  saved.value = false
  loading.value = true

  const payload = { name: form.name, email: form.email, phone: form.phone }
  if (form.password) {
    payload.password = form.password
    payload.password_confirmation = form.password_confirmation
  }

  try {
    const { data } = await authService.updateProfile(payload)
    auth.user = data.data
    saved.value = true
    form.password = ''
    form.password_confirmation = ''
    setTimeout(() => saved.value = false, 4000)
  } catch (err) {
    const data = err.response?.data
    if (data?.errors) {
      Object.keys(data.errors).forEach(k => { if (errors[k] !== undefined) errors[k] = data.errors[k][0] })
    } else {
      ui.error(data?.message ?? 'Could not update profile.')
    }
  } finally {
    loading.value = false
  }
}
</script>
