<script setup>
import ResponsiveHQ from '@/Components/Hq/Menu/ResponsiveNavigation.vue'
import ResponsiveTenant from '@/Components/Tenants/Menu/ResponsiveNavigation.vue'
import { computed } from 'vue';

const host = window.location.hostname
const menuComponent = computed(() => {
  if (host.includes('hq')) return ResponsiveHQ
  return ResponsiveTenant
});
</script>

<template>
  <component :is="menuComponent" />
</template>