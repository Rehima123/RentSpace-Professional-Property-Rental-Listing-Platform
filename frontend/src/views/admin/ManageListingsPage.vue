<template>
  <DashboardLayout :breadcrumbs="[{ label: 'Manage Listings' }]">
    <div class="mb-6"><h1 class="page-title">Manage Listings</h1><p class="page-subtitle">Review and moderate all property listings</p></div>

    <div class="card p-4 mb-5 flex flex-col sm:flex-row gap-3">
      <BaseInput v-model="search" placeholder="Search properties..." class="flex-1" @keydown.enter="fetch()">
        <template #prefix><SearchIcon :size="14" /></template>
      </BaseInput>
      <BaseSelect v-model="statusFilter" :options="statusOptions" placeholder="All statuses" class="sm:w-44" @update:model-value="fetch()" />
    </div>

    <BaseSpinner v-if="loading" :full-page="true" />

    <template v-else-if="properties.length">
      <div class="card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
              <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Property</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Owner</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Price</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-5 py-3"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="p in properties" :key="p.id" class="hover:bg-gray-50">
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-9 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                      <img v-if="p.primary_image" :src="p.primary_image" class="w-full h-full object-cover" />
                    </div>
                    <div>
                      <p class="font-medium text-gray-900 max-w-[180px] truncate">{{ p.title }}</p>
                      <p class="text-xs text-gray-400">{{ p.city }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-3.5 text-gray-600">{{ p.owner?.name ?? '—' }}</td>
                <td class="px-5 py-3.5 font-medium whitespace-nowrap">{{ formatPrice(p.price) }}</td>
                <td class="px-5 py-3.5"><BaseBadge :status="p.status">{{ capitalize(p.status) }}</BaseBadge></td>
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-1.5 justify-end">
                    <button v-if="p.status === 'pending'" class="btn btn-teal btn-sm" :disabled="acting === p.id" @click="setStatus(p, 'approved')"><CheckIcon :size="13" /></button>
                    <button v-if="p.status === 'pending'" class="btn btn-danger btn-sm" :disabled="acting === p.id" @click="setStatus(p, 'rejected')"><XIcon :size="13" /></button>
                    <button v-if="p.status === 'approved'" class="btn btn-secondary btn-sm" :disabled="acting === p.id" @click="setStatus(p, 'unavailable')"><BanIcon :size="13" /></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <Pagination :meta="pagination" @page-change="goToPage" />
    </template>

    <EmptyState v-else :icon="BuildingIcon" title="No listings found" description="Adjust your filters to find listings." />
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DashboardLayout from '@/components/layout/DashboardLayout.vue'
import BaseInput   from '@/components/ui/BaseInput.vue'
import BaseSelect  from '@/components/ui/BaseSelect.vue'
import BaseSpinner from '@/components/ui/BaseSpinner.vue'
import BaseBadge   from '@/components/ui/BaseBadge.vue'
import EmptyState  from '@/components/ui/EmptyState.vue'
import Pagination  from '@/components/ui/Pagination.vue'
import { Building2 as BuildingIcon, Search as SearchIcon, Check as CheckIcon, X as XIcon, Ban as BanIcon } from 'lucide-vue-next'
import propertyService  from '@/services/property.js'
import dashboardService from '@/services/dashboard.js'
import { useUiStore }  from '@/stores/ui.js'
import { capitalize, formatPrice } from '@/utils/format.js'
import { PROPERTY_STATUSES } from '@/utils/constants.js'

const ui           = useUiStore()
const properties   = ref([])
const pagination   = ref(null)
const loading      = ref(true)
const acting       = ref(null)
const search       = ref('')
const statusFilter = ref('pending')
const statusOptions = PROPERTY_STATUSES

async function fetch(page = 1) {
  loading.value = true
  try {
    const { data } = await dashboardService.adminProperties({ page, per_page: 20, search: search.value, status: statusFilter.value || undefined })
    properties.value = data.data
    pagination.value = data.meta
  } finally { loading.value = false }
}

async function setStatus(p, status) {
  acting.value = p.id
  try {
    await propertyService.updateStatus(p.id, status)
    p.status = status
    ui.success(`Property marked as ${status}.`)
  } catch { ui.error('Could not update status.') } finally { acting.value = null }
}

function goToPage(p) { fetch(p) }
onMounted(() => fetch())
</script>
