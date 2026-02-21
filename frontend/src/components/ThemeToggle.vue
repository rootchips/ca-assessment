<template>
  <div class="flex items-center gap-2 rounded-full bg-slate-800/80 px-2 py-1">
    <svg
      v-if="isDark"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      class="h-4 w-4 text-amber-300"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3m15.364 6.364-1.06-1.06M6.696 6.696l-1.06-1.06m12.728 0-1.06 1.06M6.696 17.304l-1.06 1.06M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
    </svg>
    <svg
      v-else
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      class="h-4 w-4 text-slate-200"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.718 9.718 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752a9.753 9.753 0 1 0 12.754 12.754Z" />
    </svg>

    <button
      type="button"
      class="relative inline-flex h-6 w-11 cursor-pointer items-center rounded-full transition"
      :class="isDark ? 'bg-[#04C968]' : 'bg-slate-500'"
      @click="toggleTheme"
    >
      <span class="sr-only">Toggle dark mode</span>
      <span
        class="inline-block h-4 w-4 transform rounded-full bg-white transition"
        :class="isDark ? 'translate-x-6' : 'translate-x-1'"
      />
    </button>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const isDark = ref(false)

const applyTheme = (value) => {
  isDark.value = value
  document.documentElement.classList.toggle('dark', value)
  localStorage.setItem('theme', value ? 'dark' : 'light')
}

const toggleTheme = () => {
  applyTheme(!isDark.value)
}

onMounted(() => {
  const saved = localStorage.getItem('theme')
  if (saved === 'dark') {
    applyTheme(true)
    return
  }

  if (saved === 'light') {
    applyTheme(false)
    return
  }

  applyTheme(window.matchMedia('(prefers-color-scheme: dark)').matches)
})
</script>