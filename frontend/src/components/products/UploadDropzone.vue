<template>
  <div class="mb-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 dark:border-slate-600 dark:bg-slate-900/40">
    <div class="mb-3 flex items-center">
      <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-slate-600 dark:text-slate-300">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V6.75m0 0-3 3m3-3 3 3M3.75 16.5v.75A2.25 2.25 0 0 0 6 19.5h12a2.25 2.25 0 0 0 2.25-2.25v-.75" />
        </svg>
        <span>Upload File</span>
      </h3>
    </div>

    <div
      v-if="!loading"
      class="mt-2 flex justify-center rounded-lg border border-dashed border-slate-300 px-6 py-10 transition hover:border-slate-400 hover:bg-slate-100/60 dark:border-white/25 dark:hover:border-white/40 dark:hover:bg-slate-800/60"
    >
      <input
        ref="statusFileInput"
        id="file-upload"
        class="sr-only"
        type="file"
        name="file-upload"
        accept=".xlsx"
        :multiple="false"
        :disabled="loading"
        @change="onStatusFileChange"
      />

      <div
        class="w-full cursor-pointer text-center"
        role="button"
        tabindex="0"
        @click="openStatusFilePicker"
        @keydown.enter.prevent="openStatusFilePicker"
        @keydown.space.prevent="openStatusFilePicker"
        @dragover.prevent
        @drop.prevent="onStatusFileDrop"
      >
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="mx-auto size-12 text-slate-300 dark:text-slate-500">
          <path d="M6.75 2.25A2.25 2.25 0 0 0 4.5 4.5v15A2.25 2.25 0 0 0 6.75 21.75h10.5A2.25 2.25 0 0 0 19.5 19.5V8.56a2.25 2.25 0 0 0-.659-1.591l-3.81-3.81a2.25 2.25 0 0 0-1.591-.659H6.75Zm6 1.72 4.03 4.03H12.75V3.97Zm-4.5 7.28a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm0 3a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm0 3a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Z"></path>
        </svg>

        <div class="mt-4 flex items-center justify-center text-sm text-slate-600 dark:text-slate-300">
          <label
            for="file-upload"
            class="relative cursor-pointer rounded-md font-semibold text-[#04C968] focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-[#04C968] hover:text-emerald-500"
          >
            <span>Upload a file</span>
          </label>
          <p class="pl-1">or drag and drop</p>
        </div>

        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">XLSX only, single file</p>
      </div>
    </div>

    <div v-else class="mt-3">
      <div class="mb-1 flex items-center justify-between text-xs text-slate-600 dark:text-slate-300">
        <span>{{ uploading ? 'Uploading...' : 'Processing...' }}</span>
        <span>{{ progressPercent }}%</span>
      </div>
      <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
        <div class="h-full bg-[#04C968] transition-all duration-500 dark:bg-[#04C968]" :style="{ width: `${progressPercent}%` }"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  loading: { type: Boolean, required: true },
  uploading: { type: Boolean, required: true },
  progressPercent: { type: Number, required: true },
})

const emit = defineEmits(['file-selected', 'invalid-multi-file'])

const statusFileInput = ref(null)

const openStatusFilePicker = () => {
  if (props.loading) return
  statusFileInput.value?.click()
}

const onStatusFileChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  emit('file-selected', file)
  event.target.value = ''
}

const onStatusFileDrop = (event) => {
  if (props.loading) return

  const files = Array.from(event.dataTransfer?.files || [])
  if (files.length > 1) {
    emit('invalid-multi-file')
    return
  }

  const file = files[0]
  if (!file) return

  emit('file-selected', file)
}
</script>
