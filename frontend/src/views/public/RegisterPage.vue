<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Left panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-navy-900 relative overflow-hidden flex-col justify-between p-12">
      <div class="absolute inset-0 bg-gradient-to-br from-navy-900 via-navy-800 to-teal-900" />
      <div class="absolute top-20 right-20 w-72 h-72 bg-teal-400/10 rounded-full blur-3xl" />

      <RouterLink :to="{ name: 'home' }" class="relative flex items-center gap-3">
        <div class="w-9 h-9 bg-teal-500 rounded-xl flex items-center justify-center">
          <HomeIcon :size="18" class="text-white" />
        </div>
        <span class="text-2xl font-black text-white">Rent<span class="text-teal-400">Space</span></span>
      </RouterLink>

      <div class="relative space-y-6">
        <div v-for="benefit in benefits" :key="benefit.title" class="flex items-start gap-4">
          <div class="w-10 h-10 bg-teal-500/20 rounded-xl flex items-center justify-center shrink-0">
            <component :is="benefit.icon" :size="18" class="text-teal-400" />
          </div>
          <div>
            <p class="font-semibold text-white text-sm">{{ benefit.title }}</p>
            <p class="text-navy-300 text-xs mt-0.5">{{ benefit.desc }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Register form -->
    <div class="flex-1 flex flex-col justify-center px-6 sm:px-12 lg:px-16 py-12 overflow-y-auto">
      <RouterLink :to="{ name: 'home' }" class="flex items-center gap-2 mb-8 lg:hidden">
        <div class="w-8 h-8 bg-navy-600 rounded-lg flex items-center justify-center">
          <HomeIcon :size="16" class="text-white" />
        </div>
        <span class="text-xl font-bold text-navy-900">Rent<span class="text-teal-500">Space</span></span>
      </RouterLink>

      <div class="max-w-sm w-full mx-auto">
        <h1 class="text-2xl font-bold text-navy-900 mb-1">Create your account</h1>
        <p class="text-gray-500 text-sm mb-6">Join RentSpace — it's free</p>

        <!-- Role selector -->
        <div class="grid grid-cols-2 gap-3 mb-6">
          <button
            v-for="role in roles"
            :key="role.value"
            type="button"
            :class="[
              'flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all text-sm',
              form.role === role.value
                ? 'border-navy-600 bg-navy-50 text-navy-700'
                : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300'
            ]"
            @click="form.role = role.value"
          >
            <component :is="role.icon" :size="22" :class="form.role === role.value ? 'text-navy-600' : 'text-gray-400'" />
            <span class="font-semibold">{{ role.label }}</span>
            <span class="text-xs opacity-70">{{ role.desc }}</span>
          </button>
        </div>
        <p v-if="errors.role" class="text-xs text-red-600 -mt-3 mb-4">{{ errors.role }}</p>

        <!-- Error banner -->
        <div v-if="errorMsg" class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4">
          <AlertCircleIcon :size="16" class="text-red-500 mt-0.5 shrink-0" />
          <p class="text-sm text-red-700">{{ errorMsg }}</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-4">
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
            placeholder="you@example.com"
            :error="errors.email"
            required
          />

          <BaseInput
            v-model="form.phone"
            label="Phone number"
            placeholder="+251 9XX XXX XXX"
            :error="errors.phone"
            hint="Optional but recommended"
          />

          <BaseInput
            v-model="form.password"
            label="Password"
            :type="showPwd ? 'text' : 'password'"
            placeholder="Min. 8 characters"
            :error="errors.password"
            required
          >
            <template #suffix>
              <button type="button" class="text-gray-400 hover:text-gray-600" @click="showPwd = !showPwd">
                <component :is="showPwd ? EyeOffIcon : EyeIcon" :size="16" />
              </button>
            </template>
          </BaseInput>

          <BaseInput
            v-model="form.password_confirmation"
            label="Confirm password"
            :type="showPwd ? 'text' : 'password'"
            placeholder="Repeat password"
            :error="errors.password_confirmation"
            required
          />

          <BaseButton type="submit" class="w-full !mt-5" :loading="loading" size="lg">
            Create Account
          </BaseButton>
        </form>

        <p class="mt-5 text-center text-sm text-gray-500">
          Already have an account?
          <RouterLink :to="{ name: 'login' }" class="text-navy-600 font-semibold hover:underline">Sign in</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useUiStore }   from '@/stores/ui.js'
import BaseInput  from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import {
  Home        as HomeIcon,
  User        as UserIcon,
  Building2   as Building2Icon,
  Eye         as EyeIcon,
  EyeOff      as EyeOffIcon,
  AlertCircle as AlertCircleIcon,
  CheckCircle2 as CheckCircle2Icon,
  Shield      as ShieldIcon,
  MapPin      as MapPinIcon,
} from 'lucide-vue-next'

const auth = useAuthStore()
const ui   = useUiStore()

const form = reactive({
  name: '', email: '', phone: '', password: '', password_confirmation: '', role: 'tenant',
})
const errors   = reactive({ name: '', email: '', phone: '', password: '', role: '' })
const errorMsg = ref('')
const loading  = ref(false)
const showPwd  = ref(false)

const roles = [
  { value: 'tenant', label: 'Tenant',       icon: UserIcon,      desc: 'Looking to rent' },
  { value: 'owner',  label: 'Property Owner',icon: Building2Icon, desc: 'List properties' },
]

const benefits = [
  { icon: MapPinIcon,      title: 'Properties across Ethiopia', desc: 'Addis Ababa, Bahir Dar, Hawassa and more.' },
  { icon: ShieldIcon,      title: 'Verified listings',          desc: 'All properties reviewed before going live.' },
  { icon: CheckCircle2Icon,title: 'Free to join',               desc: 'No subscription fees. Register in seconds.' },
]

function clearErrors() {
  Object.keys(errors).forEach(k => errors[k] = '')
  errorMsg.value = ''
}

async function handleRegister() {
  clearErrors()
  loading.value = true
  try {
    await auth.register({ ...form })
    ui.success('Account created! Welcome to RentSpace.')
  } catch (err) {
    const data = err.response?.data
    if (data?.errors) {
      Object.keys(data.errors).forEach(k => { if (errors[k] !== undefined) errors[k] = data.errors[k][0] })
    } else {
      errorMsg.value = data?.message ?? 'Registration failed.'
    }
  } finally {
    loading.value = false
  }
}
</script>
