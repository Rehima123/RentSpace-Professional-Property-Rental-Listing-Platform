<template>
  <DashboardLayout :breadcrumbs="[{ label: 'Dashboard' }]">
    <div class="mb-6">
      <h1 class="page-title">Welcome, {{ auth.userName }} 👋</h1>
      <p class="page-subtitle">Here's your rental activity overview</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <StatCard label="Total Inquiries"    :value="stats.total_inquiries    ?? 0" :icon="MessageSquareIcon" color="navy" />
      <StatCard label="Pending"            :value="stats.pending_inquiries  ?? 0" :icon="ClockIcon"         color="amber" />
      <StatCard label="Accepted"           :value="stats.accepted_inquiries ?? 0" :icon="CheckCircle2Icon"  color="green" />
      <StatCard label="Saved Properties"   :value="stats.total_favorites    ?? 0" :icon="HeartIcon"         color="red" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Inquiries -->
      <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-gray-900">Recent Inquiries</h2>
          <RouterLink :to="{ name: 'tenant.inquiries' }" class="text-xs text-navy-600 hover:underline">View all</RouterLink>
        </div>

        <BaseSpinner v-if="loading" class="py-8" />

        <div v-else-if="recentInquiries.length" class="space-y-3">
          <div v-for="inq in recentInquiries" :key="inq.id" class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
            <div class="w-10 h-10 bg-gray-200 rounded-lg overflow-hidden shrink-0">
              <img v-if="inq.property?.primary_image" :src="inq.property.primary_image" class="w-full h-full object-cover" />
              <HomeIcon v-else :size="16" class="text-gray-400 m-auto mt-2.5" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ inq.property?.title ?? 'Property' }}</p>
              <p class="text-xs text-gray-500">{{ inq.property?.city }}</p>
            </div>
            <BaseBadge :status="inq.status">{{ capitalize(inq.status) }}</BaseBadge>
          </div>
        </div>

        <EmptyState v-else :icon="MessageSquareIcon" title="No inquiries yet" description="Browse properties and send your first inquiry." class="py-8">
          <RouterLink :to="{ name: 'properties' }" class="btn btn-primary btn-sm mt-3">Browse Properties</RouterLink>
        </EmptyState>
      </div>

      <!-- Quick Actions -->
      <div class="card p-5">
        <h2 class="font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="space-y-3">
          <RouterLink :to="{ name: 'properties' }" class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 hover:border-navy-200 hover:bg-navy-50 transition-all group">
            <div class="w-9 h-9 bg-navy-50 rounded-lg flex items-center justify-center group-hover:bg-navy-100">
              <SearchIcon :size="17" class="text-navy-600" />
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-900">Browse Properties</p>
              <p class="text-xs text-gray-400">Discover new listings</p>
            </div>
            <ChevronRightIcon :size="16" class="text-gray-300 ml-auto" />
          </RouterLink>
          <RouterLink :to="{ name: 'tenant.favorites' }" class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 hover:border-red-100 hover:bg-red-50 transition-all group">
            <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center group-hover:bg-red-100">
              <HeartIcon :size="17" class="text-red-500" />
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-900">Saved Properties</p>
              <p class="text-xs text-gray-400">{{ stats.total_favorites ?? 0 }} saved</p>
            </div>
            <ChevronRightIcon :size="16" class="text-gray-300 ml-auto" />
          </RouterLink>
          <RouterLink :to="{ name: 'tenant.inquiries' }" class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 hover:border-teal-100 hover:bg-teal-50 transition-all group">
            <div class="w-9 h-9 bg-teal-50 rounded-lg flex items-center justify-center group-hover:bg-teal-100">
              <MessageSquareIcon :size="17" class="text-teal-500" />
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-900">My Inquiries</p>
              <p class="text-xs text-gray-400">{{ stats.total_inquiries ?? 0 }} total</p>
            </div>
            <ChevronRightIcon :size="16" class="text-gray-300 ml-auto" />
          </RouterLink>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardLayout from '@/components/layout/DashboardLayout.vue'
import StatCard    from '@/components/dashboard/StatCard.vue'
import BaseSpinner from '@/components/ui/BaseSpinner.vue'
import BaseBadge   from '@/components/ui/BaseBadge.vue'
import EmptyState  from '@/components/ui/EmptyState.vue'
import {
  MessageSquare as MessageSquareIcon, Clock as ClockIcon, CheckCircle2 as CheckCircle2Icon,
  Heart as HeartIcon, Home as HomeIcon, Search as SearchIcon, ChevronRight as ChevronRightIcon,
} from 'lucide-vue-next'
import dashboardService from '@/services/dashboard.js'
import { useAuthStore } from '@/stores/auth.js'
import { capitalize } from '@/utils/format.js'

const auth            = useAuthStore()
const loading         = ref(true)
const stats           = ref({})
const recentInquiries = ref([])

onMounted(async () => {
  try {
    const { data } = await dashboardService.tenant()
    stats.value           = data.stats
    recentInquiries.value = data.recent_inquiries?.data ?? []
  } catch { /* silent */ } finally { loading.value = false }
})
</script>
