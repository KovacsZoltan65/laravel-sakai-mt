<script setup>
import { ref } from 'vue'
import RecursiveMenuItem from '@/Components/Menu/RecursiveMenuItem.vue';

const menu = ref([
  {
    items: [
      {
        label: 'home',
        icon: 'pi pi-home',
        to: null,
        items: [
          {
            label: 'dashboard',
            icon: 'pi pi-th-large',
            to: 'http://company-02.mt/dashboard',
            items: []
          }
        ]
      },
      {
        label: 'administration',
        icon: 'pi pi-cog',
        to: null,
        items: [
          {
            label: 'employees',
            icon: 'pi pi-users',
            to: 'http://company-02.mt/employees',
            items: []
          },
          {
            label: 'hierarchy',
            icon: 'pi pi-share-alt',
            to: 'http://company-02.mt/hierarchy',
            items: []
          },
          {
            label: 'companies',
            icon: 'pi pi-building',
            to: 'http://company-02.mt/companies',
            items: []
          }
        ]
      },
      {
        label: 'reports',
        icon: 'pi pi-chart-line',
        to: null,
        items: [
          {
            label: 'monthly',
            icon: 'pi pi-calendar',
            to: null,
            items: [
              {
                label: 'monthly_01',
                icon: '',
                to: 'http://company-02.mt/dashboard',
                items: []
              }
            ]
          },
          {
            label: 'annual',
            icon: 'pi pi-file',
            to: 'http://company-02.mt/dashboard',
            items: []
          }
        ]
      }
    ]
  }
])
</script>

<template>
  <ul class="space-y-2 p-4 bg-white rounded-md text-sm">
    <template v-for="(group, g) in menu" :key="g">
      <RecursiveMenuItem
        v-for="(item, i) in group.items"
        :key="i"
        :item="item"
      />
    </template>
  </ul>
</template>
