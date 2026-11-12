<template>
  <BaseModal
    v-if="uiStore.confirmDialog"
    v-model="isOpen"
    :title="uiStore.confirmDialog.title"
    size="sm"
    @update:model-value="uiStore.closeConfirm"
  >
    <p class="text-gray-600 text-sm">{{ uiStore.confirmDialog.message }}</p>

    <template #footer>
      <div class="flex justify-end gap-3">
        <BaseButton variant="ghost" @click="uiStore.closeConfirm">
          {{ uiStore.confirmDialog.cancelText }}
        </BaseButton>
        <BaseButton variant="danger" @click="handleConfirm">
          {{ uiStore.confirmDialog.confirmText }}
        </BaseButton>
      </div>
    </template>
  </BaseModal>
</template>

<script setup>
import { computed } from 'vue'
import { useUiStore } from '@/stores/ui.js'
import BaseModal  from './BaseModal.vue'
import BaseButton from './BaseButton.vue'

const uiStore  = useUiStore()
const isOpen   = computed(() => !!uiStore.confirmDialog)

function handleConfirm() {
  uiStore.confirmDialog?.onConfirm?.()
  uiStore.closeConfirm()
}
</script>
