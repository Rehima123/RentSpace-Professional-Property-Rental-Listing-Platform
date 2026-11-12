<template>
  <DashboardLayout :breadcrumbs="[{ label: 'Manage Inquiries' }]">
    <div class="mb-6"><h1 class="page-title">Inquiries</h1><p class="page-subtitle">Respond to tenant inquiries on your properties</p></div>

    <BaseSpinner v-if="loading" :full-page="true" />

    <div v-else-if="inquiries.length" class="space-y-4">
      <div v-for="inq in inquiries" :key="inq.id" class="card p-5">
        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Thumbnail -->
          <div class="w-full sm:w-28 h-20 bg-gray-100 rounded-xl overflow-hidden shrink-0">
            <img v-if="inq.property?.primary_image" :src="inq.property.primary_image" class="w-full h-full object-cover" />
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3 mb-1">
              <div>
                <p class="font-semibold text-gray-900 text-sm">{{ inq.property?.title }}</p>
                <p class="text-xs text-gray-500 mb-2">From: <strong>{{ inq.tenant?.name }}</strong>
                  <span v-if="inq.tenant?.phone"> &bull; {{ inq.tenant.phone }}</span>
                </p>
              </div>
              <BaseBadge :status="inq.status">{{ capitalize(inq.status) }}</BaseBadge>
            </div>
            <p class="text-sm text-gray-700 mb-3 line-clamp-2">{{ inq.message }}</p>
            <div class="flex flex-wrap items-center gap-3">
              <span class="text-xs text-gray-400 flex items-center gap-1"><CalendarIcon :size="11" /> {{ timeAgo(inq.created_at) }}</span>
              <span v-if="inq.preferred_date" class="text-xs text-gray-400 flex items-center gap-1"><ClockIcon :size="11" /> Preferred: {{ formatDate(inq.preferred_date) }}</span>
              <div v-if="inq.status === 'pending'" class="ml-auto flex items-center gap-2">
                <button class="btn btn-teal btn-sm" :disabled="updating === inq.id" @click="updateStatus(inq, 'accepted')">
                  <CheckIcon :size="13" /> Accept
                </button>
                <button class="btn btn-danger btn-sm" :disabled="updating === inq.id" @click="updateStatus(inq, 'rejected')">
                  <XIcon :size="13" /> Reject
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <Pagination :meta="pagination" @page-change="goToPage" />
    </div>

    <EmptyState v-else :icon="MessageSquareIcon" title="No inquiries" description="Inquiries from tenants will appear here." />
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardLayout from '@/components/layout/DashboardLayout.vue'
import BaseSpinner from '@/components/ui/BaseSpinner.vue'
import BaseBadge   from '@/components/ui/BaseBadge.vue'
import EmptyState  from '@/components/ui/EmptyState.vue'
import Pagination  from '@/components/ui/Pagination.vue'
import { MessageSquare as MessageSquareIcon, Calendar as CalendarIcon, Clock as ClockIcon, Check as CheckIcon, X as XIcon } from 'lucide-vue-next'
import inquiryService from '@/services/inquiry.js'
import { useUiStore } from '@/stores/ui.js'
import { capitalize, formatDate, timeAgo } from '@/utils/format.js'

const ui         = useUiStore()
const inquiries  = ref([])
const pagination = ref(null)
const loading    = ref(true)
const updating   = ref(null)

async function fetch(page = 1) {
  loading.value = true
  try {
    const { data } = await inquiryService.ownerList({ page, per_page: 10 })
    inquiries.value  = data.data
    pagination.value = data.meta
  } finally { loading.value = false }
}

async function updateStatus(inq, status) {
  updating.value = inq.id
  try {
    await inquiryService.updateStatus(inq.id, status)
    inq.status = status
    ui.success(`Inquiry ${status}.`)
  } catch { ui.error('Could not update status.') } finally { updating.value = null }
}

function goToPage(p) { fetch(p) }
onMounted(() => fetch())
</script>
