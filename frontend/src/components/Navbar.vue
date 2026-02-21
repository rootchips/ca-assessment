<template>
  <header class="bg-slate-900 text-white shadow-lg dark:bg-slate-950">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
      <div>
        <p class="text-xs uppercase tracking-widest text-slate-300">Roslan Saidi</p>
        <h1 class="text-xl font-semibold">System Console</h1>
      </div>
      <div class="flex items-center gap-4">
        <ThemeToggle />
      </div>
    </div>
  </header>
</template>

<script setup>
import ThemeToggle from '@/components/ThemeToggle.vue'
</script>
