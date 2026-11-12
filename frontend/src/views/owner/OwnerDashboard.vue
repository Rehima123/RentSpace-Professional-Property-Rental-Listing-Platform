<template>
  <DashboardLayout :breadcrumbs="[{ label: 'Dashboard' }]">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="page-title">Owner Dashboard</h1>
        <p class="page-subtitle">Manage your properties and inquiries</p>
      </div>
      <RouterLink :to="{ name: 'owner.properties.create' }" class="btn btn-primary">
        <PlusIcon :size="16" /> Add Property
      </RouterLink>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
      <StatCard label="Total Properties" :value="stats.total_properties  ?? 0" :icon="BuildingIcon"      color="navy" />
      <StatCard label="Approved"         :value="stats.approved          ?? 0" :icon="CheckCircle2Icon"  color="green" />
      <StatCard label="Pending"          :value="stats.pending           ?? 0" :icon="ClockIcon"         color="amber" />
      <StatCard label="Rented"           :value="stats.rented            ?? 0" :icon="KeyIcon"           color="blue" />
      <StatCard label="Inquiries"        :value="stats.total_inquiries   ?? 0" :icon="MessageSquareIcon" color="teal" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Inquiries -->
      <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-gray-900">Recent Inquiries</h2>
          <RouterLink :to="{ name: 'owner.inquiries' }" class="text-xs text-navy-600 hover:underline">View all</RouterLink>
        </div>

        <BaseSpinner v-if="loading" class="py-8" />

        <div v-else-if="recentInquiries.length" class="space-y-3">
          <div v-for="inq in recentInquiries" :key="inq.id" class="flex items-start gap-3 p-3 rounded-xl bg-gray-50">
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ inq.property?.title ?? 'Property' }}</p>
              <p class="text-xs text-gray-500">From: {{ inq.tenant?.name ?? 'Tenant' }}</p>
              <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ inq.message }}</p>
            </div>
            <BaseBadge :status="inq.status">{{ capitalize(inq.status) }}</BaseBadge>
          </div>
        </div>

        <EmptyState v-else :icon="MessageSquareIcon" title="No inquiries yet" description="Inquiries will appear here when tenants contact you." class="py-8" />
      </div>

      <!-- Quick Actions -->
      <div class="card p-5">
        <h2 class="font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="space-y-3">
          <RouterLink :to="{ name: 'owner.properties.create' }" class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 hover:border-navy-200 hover:bg-navy-50 transition-all group">
            <div class="w-9 h-9 bg-navy-50 rounded-lg flex items-center justify-center group-hover:bg-navy-100">
              <PlusIcon :size="17" class="text-navy-600" />
            </div>
            <div><p class="text-sm font-semibold text-gray-900">Add New Property</p><p class="text-xs text-gray-400">List a new rental</p></div>
            <ChevronRightIcon :size="16" class="text-gray-300 ml-auto" />
          </RouterLink>
          <RouterLink :to="{ name: 'owner.properties' }" class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 hover:border-teal-100 hover:bg-teal-50 transition-all group">
            <div class="w-9 h-9 bg-teal-50 rounded-lg flex items-center justify-center group-hover:bg-teal-100">
              <BuildingIcon :size="17" class="text-teal-600" />
            </div>
            <div><p class="text-sm font-semibold text-gray-900">Manage Properties</p><p class="text-xs text-gray-400">{{ stats.total_properties ?? 0 }} listings</p></div>
            <ChevronRightIcon :size="16" class="text-gray-300 ml-auto" />
          </RouterLink>
          <RouterLink :to="{ name: 'owner.inquiries' }" class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 hover:border-amber-100 hover:bg-amber-50 transition-all group">
            <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center group-hover:bg-amber-100">
              <MessageSquareIcon :size="17" class="text-amber-600" />
            </div>
            <div><p class="text-sm font-semibold text-gray-900">Review Inquiries</p><p class="text-xs text-gray-400">{{ stats.total_inquiries ?? 0 }} inquiries</p></div>
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
  Plus as PlusIcon, Building2 as BuildingIcon, CheckCircle2 as CheckCircle2Icon,
  Clock as ClockIcon, Key as KeyIcon, MessageSquare as MessageSquareIcon, ChevronRight as ChevronRightIcon,
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
    const { data } = await dashboardService.owner()
    stats.value           = data.stats
    recentInquiries.value = data.recent_inquiries?.data ?? []
  } finally { loading.value = false }
})
</script>
