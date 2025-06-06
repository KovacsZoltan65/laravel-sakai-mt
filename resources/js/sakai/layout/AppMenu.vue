<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppMenuItem from './AppMenuItem.vue';

const model = ref([]);

onMounted(async () => {
    const response = await axios.get('/menu-items');
    console.log('response.data', response.data);
    model.value = transformMenuToSakaiModel(response.data);
    console.log('model.value', model.value);
});

function transformMenuToSakaiModel(items) {
    // A gyökérszinthez is be kell tenni egy `items` kulcsot a Sakai struktúra szerint
    return [{
        items: items.map(convertItem)
    }];
}

function convertItem(item) {
    const entry = {
        label: item.label,
        icon: item.icon || 'pi pi-fw pi-circle', // alapértelmezett ikon
    };

    if (item.route_name) {
        entry.to = route(item.route_name);
    }

    if (item.url) {
        entry.url = item.url;
        entry.target = '_blank';
    }

    if (item.children && item.children.length) {
        entry.items = item.children.map(convertItem);
    }

    if (item.can) {
        entry.can = item.can;
    }

    return entry;
}

</script>

<template>
    <ul class="layout-menu">
        <template v-for="(group, i) in model" :key="i">
            <AppMenuItem v-for="(item, j) in group.items" :key="j" :item="item" :index="j" />
        </template>
    </ul>
</template>