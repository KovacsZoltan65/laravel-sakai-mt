<script setup>
import { ref, computed, watchEffect } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  item: Object
})

const page = usePage()
const isOpen = ref(false)
const hasChildren = computed(() => props.item.items && props.item.items.length > 0)

const currentPath = computed(() => page.url || window.location.pathname)

/**
 * Checks if the current path matches the given path.
 * @param {string} target
 * @returns {boolean}
 */
const isActive = computed(() => {
  if (!props.item.to) return false
  // Create a new URL object from the given path and the current origin
  const target = new URL(props.item.to, window.location.origin).pathname
  // Check if the current path starts with the target path
  return currentPath.value.startsWith(target)
});

// Ellenőrizzük, hogy van-e aktív gyermek
const hasActiveChild = computed(() => {
  const check = (items) => {
    return items?.some(child => {
      const childPath = child.to ? new URL(child.to, window.location.origin).pathname : null
      return (
        childPath && currentPath.value.startsWith(childPath)
      ) || check(child.items || [])
    })
  }
  return check(props.item.items || [])
})

// Nyissuk ki, ha aktív vagy van aktív gyermek
watchEffect(() => {
  if (isActive.value || hasActiveChild.value) {
    isOpen.value = true
  }
})

const toggle = () => {
  isOpen.value = !isOpen.value
}
</script>

<template>
  <li>
    <!-- Szülő menü -->
    <div
      v-if="!item.to"
      class="uppercase text-gray-400 font-bold text-xs px-2 pt-4 pb-2 tracking-wide flex items-center justify-between cursor-pointer"
      :class="{ 'text-primary': isOpen }"
      @click="toggle"
    >
      <span>
        <i v-if="item.icon" :class="item.icon" class="mr-2 text-sm" />
        {{ item.label }}
      </span>
      <i v-if="item.items?.length" :class="['pi', isOpen ? 'pi-chevron-down' : 'pi-chevron-right']" class="text-xs" />
    </div>

    <!-- Kattintható menüpont -->
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

    <!-- Gyermekek -->
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
