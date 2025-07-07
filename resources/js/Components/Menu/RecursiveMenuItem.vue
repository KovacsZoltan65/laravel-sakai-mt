<script setup>
defineProps({
  item: Object
})
</script>

<template>
  <li>
    <div class="flex items-center gap-2 font-medium py-1">
      <i :class="item.icon" />
      <a
        v-if="item.to"
        :href="item.to"
        class="hover:underline text-blue-600"
      >{{ item.label }}</a>
      <span v-else class="cursor-default">{{ item.label }}</span>
    </div>

    <ul
      v-if="item.items && item.items.length"
      class="ml-4 pl-2 border-l border-gray-300"
    >
      <RecursiveMenuItem
        v-for="(child, i) in item.items"
        :key="i"
        :item="child"
      />
    </ul>
  </li>
</template>
