<script setup>
import NavigationHQ from '@/Components/Hq/Menu/Navigation.vue'
import NavigationTenant from '@/Components/Tenants/Menu/Navigation.vue'
import { computed } from 'vue';

const host = window.location.hostname

const menuComponent = computed(() => {
  if (host.includes('hq')) return NavigationHQ
  return NavigationTenant
});

</script>

<template>
  <component :is="menuComponent" />
</template>