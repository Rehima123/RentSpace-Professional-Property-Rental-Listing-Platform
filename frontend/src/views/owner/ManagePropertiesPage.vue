<template>
  <DashboardLayout :breadcrumbs="[{ label: 'My Properties' }]">
    <div class="flex items-center justify-between mb-6">
      <div><h1 class="page-title">My Properties</h1><p class="page-subtitle">Manage your rental listings</p></div>
      <RouterLink :to="{ name: 'owner.properties.create' }" class="btn btn-primary">
        <PlusIcon :size="16" /> Add Property
      </RouterLink>
    </div>

    <BaseSpinner v-if="loading" :full-page="true" />

    <template v-else-if="properties.length">
      <div class="card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
              <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Property</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Price</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Inquiries</th>
                <th class="px-5 py-3"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="p in properties" :key="p.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-9 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                      <img v-if="p.primary_image" :src="p.primary_image" class="w-full h-full object-cover" />
                      <HomeIcon v-else :size="14" class="text-gray-300 m-auto mt-2" />
                    </div>
                    <div>
                      <p class="font-medium text-gray-900 max-w-[200px] truncate">{{ p.title }}</p>
                      <p class="text-xs text-gray-400">{{ p.city }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4 font-medium text-gray-900 whitespace-nowrap">{{ formatPrice(p.price) }}</td>
                <td class="px-5 py-4"><BaseBadge :status="p.status">{{ capitalize(p.status) }}</BaseBadge></td>
                <td class="px-5 py-4 text-gray-600">{{ p.inquiries_count ?? 0 }}</td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-2 justify-end">
                    <RouterLink :to="{ name: 'property.show', params: { id: p.id } }" class="btn btn-ghost btn-sm text-gray-500"><EyeIcon :size="14" /></RouterLink>
                    <RouterLink :to="{ name: 'owner.properties.edit', params: { id: p.id } }" class="btn btn-secondary btn-sm"><EditIcon :size="14" /></RouterLink>
                    <button class="btn btn-danger btn-sm" @click="confirmDelete(p)"><TrashIcon :size="14" /></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <Pagination :meta="pagination" @page-change="goToPage" />
    </template>

    <EmptyState v-else :icon="BuildingIcon" title="No properties yet" description="Start by adding your first rental listing.">
      <RouterLink :to="{ name: 'owner.properties.create' }" class="btn btn-primary btn-sm mt-4"><PlusIcon :size="14" /> Add Property</RouterLink>
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
import { Plus as PlusIcon, Home as HomeIcon, Building2 as BuildingIcon, Eye as EyeIcon, Pencil as EditIcon, Trash2 as TrashIcon } from 'lucide-vue-next'
import propertyService from '@/services/property.js'
import { useUiStore }  from '@/stores/ui.js'
import { capitalize, formatPrice } from '@/utils/format.js'

const ui         = useUiStore()
const properties = ref([])
const pagination = ref(null)
const loading    = ref(true)

async function fetch(page = 1) {
  loading.value = true
  try {
    const { data } = await propertyService.ownerList({ page, per_page: 15 })
    properties.value = data.data
    pagination.value = data.meta
  } finally { loading.value = false }
}

function goToPage(p) { fetch(p) }

function confirmDelete(p) {
  ui.confirm({
    title: 'Delete Property',
    message: `Are you sure you want to delete "${p.title}"? This action cannot be undone.`,
    confirmText: 'Delete',
    onConfirm: async () => {
      try {
        await propertyService.destroy(p.id)
        properties.value = properties.value.filter(x => x.id !== p.id)
        ui.success('Property deleted.')
      } catch { ui.error('Could not delete property.') }
    },
  })
}

onMounted(() => fetch())
</script>
