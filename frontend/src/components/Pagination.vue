<template>
  <div class="mt-4 flex items-center justify-between text-sm">
    <p class="text-slate-500 dark:text-slate-300">
      Showing page {{ currentPage }} of {{ lastPage }} ({{ total }} records)
    </p>

    <div class="flex items-center gap-2">
      <button
        class="cursor-pointer rounded-lg border border-slate-300 px-3 py-1.5 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-600 dark:hover:bg-slate-700"
        :disabled="loading || currentPage <= 1"
        @click="emit('go-to-page', currentPage - 1)"
      >
        Prev
      </button>

      <button
        v-for="page in pages"
        :key="page"
        class="cursor-pointer rounded-lg border px-3 py-1.5 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50 dark:hover:bg-slate-700"
        :class="
          page === currentPage
            ? 'border-[#04C968] bg-[#04C968] text-white'
            : 'border-slate-300 bg-white text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100'
        "
        :disabled="loading"
        @click="emit('go-to-page', page)"
      >
        {{ page }}
      </button>

      <button
        class="cursor-pointer rounded-lg border border-slate-300 px-3 py-1.5 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-600 dark:hover:bg-slate-700"
        :disabled="loading || currentPage >= lastPage"
        @click="emit('go-to-page', currentPage + 1)"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
  currentPage: {
    type: Number,
    required: true,
  },
  lastPage: {
    type: Number,
    required: true,
  },
  total: {
    type: Number,
    required: true,
  },
})

const emit = defineEmits(['go-to-page'])

const pages = computed(() => {
  const totalPages = Number(props.lastPage ?? 1)
  const current = Number(props.currentPage ?? 1)

  if (totalPages <= 7) {
    return Array.from({ length: totalPages }, (_, index) => index + 1)
  }

  const start = Math.max(1, current - 2)
  const end = Math.min(totalPages, start + 4)
  const normalizedStart = Math.max(1, end - 4)

  return Array.from({ length: end - normalizedStart + 1 }, (_, index) => normalizedStart + index)
})
</script>