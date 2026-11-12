<template>
  <DashboardLayout :breadcrumbs="[{ label: 'My Inquiries' }]">
    <div class="flex items-center justify-between mb-6">
      <div><h1 class="page-title">My Inquiries</h1><p class="page-subtitle">Track your rental inquiries</p></div>
    </div>

    <BaseSpinner v-if="loading" :full-page="true" />

    <div v-else-if="inquiries.length" class="space-y-4">
      <div v-for="inq in inquiries" :key="inq.id" class="card p-5">
        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Property thumbnail -->
          <div class="w-full sm:w-28 h-20 bg-gray-100 rounded-xl overflow-hidden shrink-0">
            <img v-if="inq.property?.primary_image" :src="inq.property.primary_image" class="w-full h-full object-cover" />
            <div v-else class="flex items-center justify-center h-full"><HomeIcon :size="24" class="text-gray-300" /></div>
          </div>
          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3 mb-1">
              <RouterLink :to="{ name: 'property.show', params: { id: inq.property?.id } }" class="font-semibold text-gray-900 hover:text-navy-600 truncate">
                {{ inq.property?.title ?? 'Property' }}
              </RouterLink>
              <BaseBadge :status="inq.status" class="shrink-0">{{ capitalize(inq.status) }}</BaseBadge>
            </div>
            <p class="text-xs text-gray-500 mb-2">{{ inq.property?.city }} &bull; {{ formatPricePerMonth(inq.property?.price) }}</p>
            <p class="text-sm text-gray-700 line-clamp-2">{{ inq.message }}</p>
            <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
              <span class="flex items-center gap-1"><CalendarIcon :size="11" /> Sent {{ timeAgo(inq.created_at) }}</span>
              <span v-if="inq.preferred_date" class="flex items-center gap-1"><ClockIcon :size="11" /> Preferred: {{ formatDate(inq.preferred_date) }}</span>
            </div>
          </div>
        </div>
      </div>
      <Pagination :meta="pagination" @page-change="goToPage" />
    </div>

    <EmptyState v-else :icon="MessageSquareIcon" title="No inquiries yet" description="Browse properties and send your first inquiry to a property owner.">
      <RouterLink :to="{ name: 'properties' }" class="btn btn-primary btn-sm mt-4">Browse Properties</RouterLink>
    </EmptyState>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardLayout from '@/components/layout/DashboardLayout.vue'
import BaseSpinner from '@/components/ui/BaseSpinner.vue'
import BaseBadge   from '@/components/ui/BaseBadge.vue'
import EmptyState  from '@/components/ui/EmptyState.vue'
import Pagination  from '@/components/ui/Pagination.vue'
import { Home as HomeIcon, MessageSquare as MessageSquareIcon, Calendar as CalendarIcon, Clock as ClockIcon } from 'lucide-vue-next'
import inquiryService from '@/services/inquiry.js'
import { capitalize, formatPricePerMonth, formatDate, timeAgo } from '@/utils/format.js'

const inquiries  = ref([])
const pagination = ref(null)
const loading    = ref(true)

async function fetch(page = 1) {
  loading.value = true
  try {
    const { data } = await inquiryService.tenantList({ page, per_page: 10 })
    inquiries.value  = data.data
    pagination.value = data.meta
  } catch { /* silent */ } finally { loading.value = false }
}
function goToPage(p) { fetch(p); window.scrollTo({ top: 0 }) }
onMounted(() => fetch())
</script>
