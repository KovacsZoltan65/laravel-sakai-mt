<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  item: Object
})

const isOpen = ref(false)
const page = usePage()

const isActive = computed(() => {
  if (!props.item.to) return false
  const current = page.url || window.location.pathname
  return current.startsWith(new URL(props.item.to, window.location.origin).pathname)
})

const toggle = () => {
  isOpen.value = !isOpen.value
}
</script>

<template>
  <li>
    <div
      v-if="!item.to"
      class="uppercase text-gray-400 font-bold text-xs px-2 pt-4 pb-2 tracking-wide flex items-center justify-between cursor-pointer"
      @click="toggle"
    >
      <span>
        <i v-if="item.icon" :class="item.icon" class="mr-2 text-sm" />
        {{ item.label }}
      </span>
      <i v-if="item.items?.length" :class="['pi', isOpen ? 'pi-chevron-down' : 'pi-chevron-right']" class="text-xs" />
    </div>

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

    <transition name="fade">
      <ul
        v-show="isOpen"
        class="ml-4 pl-2 border-l border-gray-200"
      >
        <RecursiveMenuItem
          v-for="(child, i) in item.items"
          :key="i"
          :item="child"
        />
      </ul>
    </transition>
  </li>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  max-height: 0;
}
</style>
