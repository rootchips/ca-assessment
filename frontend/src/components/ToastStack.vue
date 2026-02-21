<template>
  <div aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-start px-4 py-6 sm:p-6">
    <TransitionGroup
      name="toast"
      tag="div"
      class="flex w-full flex-col items-end space-y-4"
    >
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="pointer-events-auto w-full max-w-sm rounded-lg bg-white shadow-lg outline-1 outline-black/5 dark:bg-slate-800 dark:outline-white/10"
      >
        <div class="p-4">
          <div class="flex items-start">
            <div class="shrink-0">
              <svg
                v-if="toast.type === 'success'"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                aria-hidden="true"
                class="size-6 text-emerald-400"
              >
                <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <svg
                v-else
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                aria-hidden="true"
                class="size-6 text-rose-400"
              >
                <path d="M12 9v3.75m0 3.75h.008v.008H12v-.008Zm9 1.5a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
              <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ toast.type === 'success' ? 'Success' : 'Error' }}</p>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-300">{{ toast.message }}</p>
            </div>
            <div class="ml-4 flex shrink-0">
              <button
                type="button"
                class="inline-flex rounded-md text-slate-400 hover:text-slate-500 focus:outline-2 focus:outline-offset-2 focus:outline-[#04C968] dark:text-slate-300 dark:hover:text-slate-100"
                @click="emit('close', toast.id)"
              >
                <span class="sr-only">Close</span>
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-5">
                  <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
defineProps({
  toasts: {
    type: Array,
    required: true,
  },
})

const emit = defineEmits(['close'])
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: opacity 220ms ease, transform 220ms ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translate3d(0, 8px, 0);
}

@media (min-width: 640px) {
  .toast-enter-from,
  .toast-leave-to {
    transform: translate3d(8px, 0, 0);
  }
}

.toast-move {
  transition: transform 220ms ease;
}
</style>
