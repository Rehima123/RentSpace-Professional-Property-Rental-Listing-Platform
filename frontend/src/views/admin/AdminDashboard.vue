<template>
  <DashboardLayout :breadcrumbs="[{ label: 'Admin Dashboard' }]">
    <div class="mb-6">
      <h1 class="page-title">Admin Dashboard</h1>
      <p class="page-subtitle">Platform-wide overview and management</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <StatCard label="Total Users"      :value="stats.total_users       ?? 0" :icon="UsersIcon"       color="navy" />
      <StatCard label="Owners"           :value="stats.total_owners      ?? 0" :icon="BuildingIcon"    color="teal" />
      <StatCard label="Tenants"          :value="stats.total_tenants     ?? 0" :icon="UserIcon"        color="blue" />
      <StatCard label="Total Properties" :value="stats.total_properties  ?? 0" :icon="HomeIcon"        color="navy" />
      <StatCard label="Pending Approvals":value="stats.pending_approvals ?? 0" :icon="ClockIcon"       color="amber" />
      <StatCard label="Approved"         :value="stats.approved_listings ?? 0" :icon="CheckCircle2Icon":color="green" />
      <StatCard label="Rented"           :value="stats.rented_properties ?? 0" :icon="KeyIcon"         color="blue" />
      <StatCard label="Total Inquiries"  :value="stats.total_inquiries   ?? 0" :icon="MessageSquareIcon" color="teal" />
    </div>

    <!-- Pending Listings -->
    <div class="card overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">Pending Listings <span v-if="stats.pending_approvals" class="ml-1.5 badge badge-pending">{{ stats.pending_approvals }}</span></h2>
        <RouterLink :to="{ name: 'admin.listings' }" class="text-xs text-navy-600 hover:underline">View all listings</RouterLink>
      </div>

      <BaseSpinner v-if="loading" class="py-10" />

      <div v-else-if="pendingListings.length" class="divide-y divide-gray-50">
        <div v-for="p in pendingListings" :key="p.id" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50">
          <div class="w-14 h-10 bg-gray-100 rounded-lg overflow-hidden shrink-0">
            <img v-if="p.primary_image" :src="p.primary_image" class="w-full h-full object-cover" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">{{ p.title }}</p>
            <p class="text-xs text-gray-400">{{ p.owner?.name }} &bull; {{ p.city }}</p>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <button class="btn btn-teal btn-sm" :disabled="approving === p.id" @click="approve(p)">
              <CheckIcon :size="13" /> Approve
            </button>
            <button class="btn btn-danger btn-sm" :disabled="approving === p.id" @click="reject(p)">
              <XIcon :size="13" /> Reject
            </button>
          </div>
        </div>
      </div>

      <EmptyState v-else :icon="CheckCircle2Icon" title="All caught up!" description="No listings pending approval." class="py-10" />
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardLayout from '@/components/layout/DashboardLayout.vue'
import StatCard    from '@/components/dashboard/StatCard.vue'
import BaseSpinner from '@/components/ui/BaseSpinner.vue'
import EmptyState  from '@/components/ui/EmptyState.vue'
import {
  Users as UsersIcon, Building2 as BuildingIcon, User as UserIcon, Home as HomeIcon,
  Clock as ClockIcon, CheckCircle2 as CheckCircle2Icon, Key as KeyIcon,
  MessageSquare as MessageSquareIcon, Check as CheckIcon, X as XIcon,
} from 'lucide-vue-next'
import dashboardService from '@/services/dashboard.js'
import propertyService  from '@/services/property.js'
import { useUiStore }   from '@/stores/ui.js'

const ui             = useUiStore()
const loading        = ref(true)
const stats          = ref({})
const pendingListings= ref([])
const approving      = ref(null)

onMounted(async () => {
  try {
    const { data } = await dashboardService.admin()
    stats.value          = data.stats
    pendingListings.value = data.pending_listings?.data ?? []
  } finally { loading.value = false }
})

async function approve(p) {
  approving.value = p.id
  try {
    await propertyService.updateStatus(p.id, 'approved')
    pendingListings.value = pendingListings.value.filter(x => x.id !== p.id)
    stats.value.pending_approvals = Math.max(0, (stats.value.pending_approvals ?? 1) - 1)
    stats.value.approved_listings = (stats.value.approved_listings ?? 0) + 1
    ui.success('Property approved.')
  } catch { ui.error('Could not approve.') } finally { approving.value = null }
}

async function reject(p) {
  approving.value = p.id
  try {
    await propertyService.updateStatus(p.id, 'rejected')
    pendingListings.value = pendingListings.value.filter(x => x.id !== p.id)
    stats.value.pending_approvals = Math.max(0, (stats.value.pending_approvals ?? 1) - 1)
    ui.info('Property rejected.')
  } catch { ui.error('Could not reject.') } finally { approving.value = null }
}
</script>
