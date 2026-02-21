<template>
  <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
      <thead class="bg-slate-50/70 text-slate-800 dark:bg-slate-900/40 dark:text-slate-100">
        <tr>
          <th class="px-4 py-3 text-left font-medium" v-for="column in columns" :key="column.key">
            <button class="inline-flex items-center gap-1" @click="emit('sort', column.key)">
              <span :class="headerTextClass(column.key)">{{ column.label }}</span>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .53.22l3 3a.75.75 0 1 1-1.06 1.06L10.75 5.56V11a.75.75 0 0 1-1.5 0V5.56L7.53 7.28a.75.75 0 0 1-1.06-1.06l3-3A.75.75 0 0 1 10 3Z" clip-rule="evenodd" :class="sortArrowClass(column.key, 'asc')" />
                <path fill-rule="evenodd" d="M10 17a.75.75 0 0 1-.53-.22l-3-3a.75.75 0 1 1 1.06-1.06l1.72 1.72V9a.75.75 0 0 1 1.5 0v5.44l1.72-1.72a.75.75 0 0 1 1.06 1.06l-3 3A.75.75 0 0 1 10 17Z" clip-rule="evenodd" :class="sortArrowClass(column.key, 'desc')" />
              </svg>
            </button>
          </th>
        </tr>
      </thead>

      <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-800">
        <template v-if="loadingSkeleton">
          <tr v-for="n in 6" :key="`skeleton-${n}`" class="animate-pulse">
            <td class="px-4 py-3"><div class="h-4 w-16 rounded bg-slate-200"></div></td>
            <td class="px-4 py-3"><div class="h-4 w-20 rounded bg-slate-200"></div></td>
            <td class="px-4 py-3"><div class="h-4 w-20 rounded bg-slate-200"></div></td>
            <td class="px-4 py-3"><div class="h-4 w-24 rounded bg-slate-200"></div></td>
            <td class="px-4 py-3"><div class="h-4 w-20 rounded bg-slate-200"></div></td>
            <td class="px-4 py-3"><div class="h-5 w-12 rounded-full bg-slate-200"></div></td>
          </tr>
        </template>

        <template v-else>
          <tr v-for="product in products" :key="product.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/60">
            <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-100">{{ product.product_id }}</td>
            <td class="px-4 py-3">{{ product.type }}</td>
            <td class="px-4 py-3">{{ product.brand }}</td>
            <td class="px-4 py-3">{{ product.model }}</td>
            <td class="px-4 py-3">{{ product.capacity }}</td>
            <td class="px-4 py-3">
              <span class="rounded-full bg-[#04C968]/15 px-2 py-1 text-xs font-semibold text-[#047A3F] dark:bg-[#04C968]/30 dark:text-[#8BFFBE]">
                {{ product.quantity }}
              </span>
            </td>
          </tr>
          <tr v-if="products.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-300">No products found.</td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script setup>
const props = defineProps({
  products: { type: Array, required: true },
  loadingSkeleton: { type: Boolean, required: true },
  sortBy: { type: String, required: true },
  sortDirectionValue: { type: String, required: true },
})

const emit = defineEmits(['sort'])

const columns = [
  { key: 'product_id', label: 'Product ID' },
  { key: 'type', label: 'Type' },
  { key: 'brand', label: 'Brand' },
  { key: 'model', label: 'Model' },
  { key: 'capacity', label: 'Capacity' },
  { key: 'quantity', label: 'Quantity' },
]

const isSorted = (column) => props.sortBy === column

const sortArrowClass = (column, direction) => {
  if (!isSorted(column)) return 'text-slate-400 dark:text-slate-500'
  return props.sortDirectionValue === direction ? 'text-slate-800 dark:text-slate-100' : 'text-slate-300 dark:text-slate-600'
}

const headerTextClass = (column) =>
  isSorted(column)
    ? 'font-bold text-slate-800 dark:text-slate-100'
    : 'font-medium text-slate-700 dark:text-slate-300'
</script>
