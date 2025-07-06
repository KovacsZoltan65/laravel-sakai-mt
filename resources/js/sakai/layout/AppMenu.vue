<script setup>
import { ref, onMounted } from 'vue';
import MenuService from "@/services/MenuService.js";
import AppMenuItem from './AppMenuItem.vue';

const model = ref([]);

onMounted(async () => {
    const response = await MenuService.getMenu();
    model.value = transformMenuToSakaiModel(response.data);
    //console.log('model.value', model.value);
});

function routeExists(name) {
  try {
    route(name);
    return true;
  } catch (error) {
    return false;
  }
}

function transformMenuToSakaiModel(items) {
    // A gyökérszinthez is be kell tenni egy `items` kulcsot a Sakai struktúra szerint
    return [{
        items: items.map(convertItem).filter(Boolean)
    }];
}

function convertItem(item) {
    // Ellenőrizzük, hogy van-e érvényes route
    const routeName = item.route_name;

    let url = null;
    if (routeName && routeExists(routeName)) {
        url = route(routeName);
    } else if (routeName) {
        console.warn(`⚠️ Hibás vagy nem létező route: ${routeName}`);
        return null; // vagy: item.hidden = true
    }

    return {
        label: item.label,
        icon: item.icon,
        to: url,
        items: (item.children ?? [])
            .map(convertItem)
            .filter(Boolean)
    };
}

/*
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
*/
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(group, i) in model" :key="i">
            <AppMenuItem
                v-show="!item?.can || has(item.can)"
                v-for="(item, j) in group.items"
                :key="j"
                :item="item"
                :index="j" />
        </template>
    </ul>
</template>
