<template>
  <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <div>
        <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Products</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-300">Display all data from product master list.</p>
      </div>
      <button
        class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-rose-500 disabled:opacity-50"
        :disabled="store.loading || store.products.length === 0"
        @click="openClearAllModal"
      >
        Clear All Products
      </button>
    </div>

    <UploadDropzone
      :loading="isUploadSkeletonLoading"
      :uploading="uploadStore.loading"
      :progress-percent="uploadProgressPercent"
      @file-selected="onFileSelected"
      @invalid-multi-file="onInvalidMultiFile"
    />

    <ProductFilters
      v-if="showDataSection"
      :search="searchInput"
      :per-page="perPageInput"
      @update:search="searchInput = $event"
      @update:per-page="perPageInput = $event"
    />

    <ProductsTable
      v-if="showDataSection"
      :products="store.products"
      :loading-skeleton="isUploadSkeletonLoading"
      :sort-by="store.filters.sort_by"
      :sort-direction-value="store.filters.sort_direction"
      @sort="sort"
    />

    <Pagination
      v-if="showDataSection"
      :loading="store.loading"
      :current-page="Number(store.meta.current_page || 1)"
      :last-page="Number(store.meta.last_page || 1)"
      :total="Number(store.meta.total || 0)"
      @go-to-page="store.fetchProducts"
    />

    <ProductsEmptyState v-else />
  </section>

  <AlertModal
    :open="isClearAllModalOpen"
    :loading="isClearingAll"
    title="Clear all products?"
    title-id="clear-all-dialog-title"
    message="This action will permanently remove all product records. This action cannot be undone."
    confirm-text="Clear all"
    loading-text="Clearing..."
    cancel-text="Cancel"
    @close="closeClearAllModal"
    @confirm="confirmClearAllProducts"
  />

  <ToastStack :toasts="toasts" @close="removeToast" />
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useProductStore } from '@/stores/productStore'
import { useUploadStore } from '@/stores/uploadStore'
import UploadDropzone from '@/components/products/UploadDropzone.vue'
import ProductFilters from '@/components/products/ProductFilters.vue'
import ProductsTable from '@/components/products/ProductsTable.vue'
import ProductsEmptyState from '@/components/products/ProductsEmptyState.vue'
import Pagination from '@/components/Pagination.vue'
import AlertModal from '@/components/AlertModal.vue'
import ToastStack from '@/components/ToastStack.vue'

const store = useProductStore()
const uploadStore = useUploadStore()
const searchInput = ref('')
const perPageInput = ref(10)
const toasts = ref([])
const isUploadSkeletonLoading = ref(false)
const isClearAllModalOpen = ref(false)
const isFiltersReady = ref(false)
const isClearingAll = ref(false)
let filterDebounceTimer = null
let toastSeed = 0

const sort = async (column) => {
  await store.applySorting(column)
}

const showDataSection = computed(() => {
  const total = Number(store.meta.total ?? 0)
  return isUploadSkeletonLoading.value || total > 0
})

const uploadProgressPercent = computed(() => {
  if (!isUploadSkeletonLoading.value) return 0

  if (uploadStore.loading) {
    return Math.max(5, Number(uploadStore.uploadProgress || 0))
  }

  return 100
})

const pushToast = (type, message) => {
  if (!message) return

  toastSeed += 1
  const id = toastSeed
  toasts.value.push({ id, type, message })

  setTimeout(() => {
    removeToast(id)
  }, 3500)
}

const removeToast = (id) => {
  toasts.value = toasts.value.filter((toast) => toast.id !== id)
}

const processStatusFile = async (file) => {
  if (!file) return

  uploadStore.uploadProgress = 0
  isUploadSkeletonLoading.value = true

  const uploadId = await uploadStore.uploadXlsx(file)
  if (uploadId) {
    let attempts = 0
    let isProcessed = false

    while (attempts < 240) {
      await uploadStore.fetchProgress(uploadId)
      uploadStore.uploadProgress = Number(uploadStore.progress[uploadId] ?? 0)

      if (uploadStore.uploadProgress >= 100) {
        isProcessed = true
        break
      }

      attempts += 1
      await new Promise((resolve) => setTimeout(resolve, 500))
    }

    if (isProcessed) {
      pushToast('success', 'Upload processed successfully.')
      await store.fetchProducts(1)
    } else {
      pushToast('error', 'Upload is taking longer than expected. Please refresh and check again.')
    }
  }

  isUploadSkeletonLoading.value = false
  uploadStore.uploadProgress = 0
}

const onFileSelected = async (file) => {
  await processStatusFile(file)
}

const onInvalidMultiFile = () => {
  uploadStore.error = 'Please upload only one file at a time.'
}

const openClearAllModal = () => {
  isClearAllModalOpen.value = true
}

const closeClearAllModal = () => {
  if (isClearingAll.value) return
  isClearAllModalOpen.value = false
}

const confirmClearAllProducts = async () => {
  if (isClearingAll.value) return

  isClearingAll.value = true

  try {
    await store.clearAllProducts()
    isClearAllModalOpen.value = false
  } finally {
    isClearingAll.value = false
  }
}

watch(
  () => uploadStore.message,
  (message) => {
    if (!message) return
    pushToast('success', message)
    uploadStore.message = null
  }
)

watch(
  () => uploadStore.error,
  (message) => {
    if (!message) return
    pushToast('error', message)
    uploadStore.error = null
  }
)

watch(
  () => store.error,
  (message) => {
    if (!message) return
    pushToast('error', message)
    store.error = null
  }
)

watch(
  () => store.message,
  (message) => {
    if (!message || message === 'Products fetched successfully.') return
    pushToast('success', message)
    store.message = null
  }
)

watch([searchInput, perPageInput], ([search, perPage]) => {
  if (!isFiltersReady.value) return

  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer)
  }

  filterDebounceTimer = setTimeout(async () => {
    store.filters.search = search
    store.filters.per_page = Number(perPage || 10)
    store.filters.page = 1
    await store.fetchProducts(1)
  }, 450)
})

onMounted(async () => {
  searchInput.value = store.filters.search || ''
  perPageInput.value = Number(store.filters.per_page || 10)
  await store.fetchProducts(1)
  isFiltersReady.value = true
})
</script>
