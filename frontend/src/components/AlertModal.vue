<template>
  <Transition name="modal-backdrop">
    <div
      v-if="open"
      class="fixed inset-0 z-40 bg-slate-500/75 dark:bg-slate-900/70"
      @click="emit('close')"
    ></div>
  </Transition>

  <Transition name="modal-panel">
    <div v-if="open" class="fixed inset-0 z-50 overflow-y-auto" :aria-labelledby="titleId" role="dialog" aria-modal="true">
      <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0" @click.self="emit('close')">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl sm:my-8 sm:p-6 dark:bg-slate-800 dark:outline dark:-outline-offset-1 dark:outline-white/10">
          <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
            <button
              type="button"
              class="rounded-md bg-white text-slate-400 hover:text-slate-500 focus:outline-2 focus:outline-offset-2 focus:outline-[#04C968] dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-300"
              :disabled="loading"
              @click="emit('close')"
            >
              <span class="sr-only">Close</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>

          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-rose-100 sm:mx-0 sm:size-10 dark:bg-rose-500/10">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-rose-600 dark:text-rose-400">
                <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>

            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 :id="titleId" class="text-base font-semibold text-slate-900 dark:text-white">{{ title }}</h3>
              <div class="mt-2">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ message }}</p>
              </div>
            </div>
          </div>

          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              class="inline-flex w-full justify-center rounded-md bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-500 sm:ml-3 sm:w-auto disabled:opacity-60 dark:bg-rose-500 dark:hover:bg-rose-400"
              :disabled="loading"
              @click="emit('confirm')"
            >
              {{ loading ? loadingText : confirmText }}
            </button>
            <button
              type="button"
              class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 inset-ring-1 inset-ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto dark:bg-white/10 dark:text-white dark:inset-ring-white/5 dark:hover:bg-white/20"
              :disabled="loading"
              @click="emit('close')"
            >
              {{ cancelText }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  open: { type: Boolean, required: true },
  loading: { type: Boolean, default: false },
  title: { type: String, required: true },
  message: { type: String, required: true },
  titleId: { type: String, default: 'alert-modal-title' },
  confirmText: { type: String, default: 'Confirm' },
  cancelText: { type: String, default: 'Cancel' },
  loadingText: { type: String, default: 'Processing...' },
})

const emit = defineEmits(['close', 'confirm'])
</script>

<style scoped>
.modal-backdrop-enter-active,
.modal-backdrop-leave-active {
  transition: opacity 200ms ease;
}

.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
  opacity: 0;
}

.modal-panel-enter-active,
.modal-panel-leave-active {
  transition: opacity 220ms ease, transform 220ms ease;
}

.modal-panel-enter-from,
.modal-panel-leave-to {
  opacity: 0;
  transform: translate3d(0, 12px, 0) scale(0.98);
}
</style>