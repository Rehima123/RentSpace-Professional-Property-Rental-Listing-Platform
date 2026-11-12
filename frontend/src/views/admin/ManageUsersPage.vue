<template>
  <DashboardLayout :breadcrumbs="[{ label: 'Manage Users' }]">
    <div class="flex items-center justify-between mb-6">
      <div><h1 class="page-title">Manage Users</h1><p class="page-subtitle">View and moderate platform users</p></div>
    </div>

    <!-- Filters -->
    <div class="card p-4 mb-5 flex flex-col sm:flex-row gap-3">
      <BaseInput v-model="search" placeholder="Search by name or email..." class="flex-1" @keydown.enter="fetch()">
        <template #prefix><SearchIcon :size="14" /></template>
      </BaseInput>
      <BaseSelect v-model="roleFilter" :options="roleOptions" placeholder="All roles" class="sm:w-40" @update:model-value="fetch()" />
      <BaseSelect v-model="statusFilter" :options="statusOptions" placeholder="All statuses" class="sm:w-40" @update:model-value="fetch()" />
    </div>

    <BaseSpinner v-if="loading" :full-page="true" />

    <template v-else-if="users.length">
      <div class="card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
              <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Role</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Properties</th>
                <th class="px-5 py-3"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="u in users" :key="u.id" class="hover:bg-gray-50">
                <td class="px-5 py-3.5">
                  <div>
                    <p class="font-medium text-gray-900">{{ u.name }}</p>
                    <p class="text-xs text-gray-400">{{ u.email }}</p>
                  </div>
                </td>
                <td class="px-5 py-3.5"><BaseBadge :variant="u.role === 'admin' ? 'primary' : u.role === 'owner' ? 'teal' : 'default'">{{ capitalize(u.role) }}</BaseBadge></td>
                <td class="px-5 py-3.5"><BaseBadge :variant="u.status === 'active' ? 'success' : 'danger'">{{ capitalize(u.status) }}</BaseBadge></td>
                <td class="px-5 py-3.5 text-gray-600">{{ u.properties_count ?? 0 }}</td>
                <td class="px-5 py-3.5">
                  <button
                    v-if="u.role !== 'admin'"
                    :class="['btn btn-sm', u.status === 'active' ? 'btn-danger' : 'btn-teal']"
                    :disabled="toggling === u.id"
                    @click="toggleStatus(u)"
                  >{{ u.status === 'active' ? 'Suspend' : 'Reactivate' }}</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <Pagination :meta="pagination" @page-change="goToPage" />
    </template>

    <EmptyState v-else :icon="UsersIcon" title="No users found" description="Adjust your filters to find users." />
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
import { Users as UsersIcon, Search as SearchIcon } from 'lucide-vue-next'
import dashboardService from '@/services/dashboard.js'
import { useUiStore }   from '@/stores/ui.js'
import { capitalize }   from '@/utils/format.js'

const ui           = useUiStore()
const users        = ref([])
const pagination   = ref(null)
const loading      = ref(true)
const toggling     = ref(null)
const search       = ref('')
const roleFilter   = ref('')
const statusFilter = ref('')

const roleOptions   = [{ value: 'admin', label: 'Admin' }, { value: 'owner', label: 'Owner' }, { value: 'tenant', label: 'Tenant' }]
const statusOptions = [{ value: 'active', label: 'Active' }, { value: 'suspended', label: 'Suspended' }]

async function fetch(page = 1) {
  loading.value = true
  try {
    const { data } = await dashboardService.adminUsers({ page, per_page: 20, search: search.value, role: roleFilter.value, status: statusFilter.value })
    users.value      = data.data
    pagination.value = data.meta
  } finally { loading.value = false }
}

async function toggleStatus(u) {
  toggling.value = u.id
  const newStatus = u.status === 'active' ? 'suspended' : 'active'
  try {
    await dashboardService.adminUpdateUserStatus(u.id, newStatus)
    u.status = newStatus
    ui.success(`User ${newStatus}.`)
  } catch { ui.error('Could not update user.') } finally { toggling.value = null }
}

function goToPage(p) { fetch(p) }
onMounted(() => fetch())
</script>
