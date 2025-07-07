<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  item: Object
})

const page = usePage()

const isActive = computed(() => {
  if (!props.item.to) return false
  const current = page.url || window.location.pathname
  return current.startsWith(new URL(props.item.to, window.location.origin).pathname)
})
</script>

<template>
  <li>
    <!-- Főcím: ha nincs to -->
    <div
      v-if="!item.to"
      class="uppercase text-gray-400 font-bold text-xs px-2 pt-4 pb-2 tracking-wide"
    >
      <i v-if="item.icon" :class="item.icon" class="mr-2 text-sm" />
      {{ item.label }}
    </div>

    <!-- Link menüpont -->
    <a
      v-else
      :href="item.to"
      class="flex items-center gap-2 px-3 py-1 rounded hover:bg-gray-100 transition-all duration-150"
      :class="{
        'text-primary font-bold bg-gray-50': isActive,
        'text-gray-700': !isActive
      }"
    >
      <i :class="item.icon" class="text-base" />
      <span class="text-sm">{{ item.label }}</span>
    </a>

    <!-- Rekurzív gyerekek -->
    <ul
      v-if="item.items && item.items.length"
      class="ml-4 pl-2 border-l border-gray-200"
    >
      <RecursiveMenuItem
        v-for="(child, i) in item.items"
        :key="i"
        :item="child"
      />
    </ul>
  </li>
</template>
