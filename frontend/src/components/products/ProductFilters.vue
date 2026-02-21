<template>
  <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-900/40">
    <div class="mb-3 flex items-center">
      <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-slate-600 dark:text-slate-300">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.347.702 7.606 1.936.357.195.53.61.405 1.002A18.6 18.6 0 0 1 16.5 12v5.25a.75.75 0 0 1-.22.53l-2.25 2.25A.75.75 0 0 1 12.75 19.5V12A18.6 18.6 0 0 1 4 5.938a.75.75 0 0 1 .405-1.002A15.96 15.96 0 0 1 12 3Z" />
        </svg>
        <span>Filters</span>
      </h3>
    </div>

    <div class="grid gap-3 md:grid-cols-12">
      <div class="md:col-span-10">
        <label class="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Search</label>
        <div class="relative">
          <input
            :value="search"
            placeholder="Search by Product ID, Type, Brand, Model, Capacity, Quantity..."
            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 pr-10 text-sm outline-none transition focus:border-[#04C968] focus:ring-2 focus:ring-[#04C968]/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-[#04C968]/30"
            @input="emit('update:search', $event.target.value)"
          />
          <button
            v-if="search"
            class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-100"
            aria-label="Clear search"
            @click="emit('update:search', '')"
          >
            ✕
          </button>
        </div>
      </div>

      <div class="md:col-span-2">
        <label class="mb-1 block text-xs font-medium text-slate-600 dark:text-slate-300">Per page</label>
        <select
          :value="perPage"
          class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-[#04C968] focus:ring-2 focus:ring-[#04C968]/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-[#04C968]/30"
          @change="emit('update:perPage', Number($event.target.value))"
        >
          <option :value="10">10</option>
          <option :value="25">25</option>
          <option :value="50">50</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  search: { type: String, required: true },
  perPage: { type: Number, required: true },
})

const emit = defineEmits(['update:search', 'update:perPage'])
</script>
