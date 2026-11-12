<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Left decorative panel (desktop) -->
    <div class="hidden lg:flex lg:w-1/2 bg-navy-900 relative overflow-hidden flex-col justify-between p-12">
      <div class="absolute inset-0 bg-gradient-to-br from-navy-900 via-navy-800 to-teal-900" />
      <div class="absolute top-20 left-20 w-72 h-72 bg-teal-400/10 rounded-full blur-3xl" />
      <div class="absolute bottom-20 right-20 w-96 h-96 bg-navy-400/10 rounded-full blur-3xl" />

      <!-- Brand -->
      <div class="relative flex items-center gap-3">
        <div class="w-9 h-9 bg-teal-500 rounded-xl flex items-center justify-center">
          <HomeIcon :size="18" class="text-white" />
        </div>
        <span class="text-2xl font-black text-white">Rent<span class="text-teal-400">Space</span></span>
      </div>

      <!-- Quote -->
      <div class="relative">
        <p class="text-3xl font-bold text-white leading-tight mb-6">
          "Find your perfect space<br/>across Ethiopia."
        </p>
        <div class="flex items-center gap-4">
          <div class="flex -space-x-2">
            <div v-for="i in 4" :key="i" class="w-9 h-9 rounded-full border-2 border-navy-700 bg-navy-600 flex items-center justify-center">
              <UserIcon :size="14" class="text-navy-200" />
            </div>
          </div>
          <p class="text-sm text-navy-300">Trusted by <span class="text-teal-400 font-semibold">10,000+</span> users across Ethiopia</p>
        </div>
      </div>
    </div>

    <!-- Right login form -->
    <div class="flex-1 flex flex-col justify-center px-6 sm:px-12 lg:px-16 py-12">
      <!-- Mobile brand -->
      <RouterLink :to="{ name: 'home' }" class="flex items-center gap-2 mb-8 lg:hidden">
        <div class="w-8 h-8 bg-navy-600 rounded-lg flex items-center justify-center">
          <HomeIcon :size="16" class="text-white" />
        </div>
        <span class="text-xl font-bold text-navy-900">Rent<span class="text-teal-500">Space</span></span>
      </RouterLink>

      <div class="max-w-sm w-full mx-auto">
        <h1 class="text-2xl font-bold text-navy-900 mb-1">Welcome back</h1>
        <p class="text-gray-500 text-sm mb-8">Sign in to your RentSpace account</p>

        <!-- Error alert -->
        <div v-if="errorMsg" class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4">
          <AlertCircleIcon :size="16" class="text-red-500 mt-0.5 shrink-0" />
          <p class="text-sm text-red-700">{{ errorMsg }}</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
          <BaseInput
            v-model="form.email"
            label="Email address"
            type="email"
            placeholder="you@example.com"
            :error="errors.email"
            required
            autocomplete="email"
          />

          <div>
            <BaseInput
              v-model="form.password"
              label="Password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="Your password"
              :error="errors.password"
              required
              autocomplete="current-password"
            >
              <template #suffix>
                <button type="button" class="text-gray-400 hover:text-gray-600" @click="showPassword = !showPassword">
                  <component :is="showPassword ? EyeOffIcon : EyeIcon" :size="16" />
                </button>
              </template>
            </BaseInput>
          </div>

          <BaseButton type="submit" class="w-full" :loading="loading" size="lg">
            Sign in
          </BaseButton>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
          Don't have an account?
          <RouterLink :to="{ name: 'register' }" class="text-navy-600 font-semibold hover:underline">Create one</RouterLink>
        </p>

        <!-- Demo credentials hint -->
        <div class="mt-8 p-4 bg-amber-50 border border-amber-200 rounded-xl">
          <p class="text-xs font-semibold text-amber-700 mb-2">Demo credentials:</p>
          <div class="space-y-1">
            <p class="text-xs text-amber-600 cursor-pointer hover:underline" @click="fillDemo('admin@rentspace.et')">Admin: admin@rentspace.et / password</p>
            <p class="text-xs text-amber-600 cursor-pointer hover:underline" @click="fillDemo('owner1@rentspace.et')">Owner: owner1@rentspace.et / password</p>
            <p class="text-xs text-amber-600 cursor-pointer hover:underline" @click="fillDemo('tenant1@rentspace.et')">Tenant: tenant1@rentspace.et / password</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useUiStore } from '@/stores/ui.js'
import BaseInput  from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import {
  Home         as HomeIcon,
  User         as UserIcon,
  Eye          as EyeIcon,
  EyeOff       as EyeOffIcon,
  AlertCircle  as AlertCircleIcon,
} from 'lucide-vue-next'

const auth  = useAuthStore()
const ui    = useUiStore()

const form = reactive({ email: '', password: '' })
const errors = reactive({ email: '', password: '' })
const errorMsg    = ref('')
const loading     = ref(false)
const showPassword= ref(false)

function clearErrors() {
  errors.email = ''; errors.password = ''; errorMsg.value = ''
}

function fillDemo(email) {
  form.email = email
  form.password = 'password'
}

async function handleLogin() {
  clearErrors()
  loading.value = true

  try {
    await auth.login({ email: form.email, password: form.password })
    ui.success('Welcome back!')
  } catch (err) {
    const data = err.response?.data
    if (data?.errors) {
      if (data.errors.email)    errors.email    = data.errors.email[0]
      if (data.errors.password) errors.password = data.errors.password[0]
    } else {
      errorMsg.value = data?.message ?? 'Login failed. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>
