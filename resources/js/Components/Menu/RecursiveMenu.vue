<script setup>
import { onMounted, ref } from 'vue'
import RecursiveMenuItem from '@/Components/Menu/RecursiveMenuItem.vue'
import MenuService from '@/services/MenuService.js';

//const menu = MenuService.getMenu();
const menu = ref(null);

onMounted(async () => {
    console.log('onMounted');
    //const data = await MenuService.getMenu();
    const data = await MenuService.getMenu_api();
    menu.value = data;
});
</script>

<template>
    <ul class="space-y-2 p-4 bg-white rounded-md text-sm">
        <template v-for="(group, g) in menu" :key="g">
            <RecursiveMenuItem v-for="(item, i) in group.items" :key="i" :item="item" />
        </template>
    </ul>
</template>
