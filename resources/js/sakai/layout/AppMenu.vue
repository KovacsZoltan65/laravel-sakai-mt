<script setup>
import { ref, onMounted } from 'vue';
import MenuService from "@/services/MenuService.js";
import AppMenuItem from './AppMenuItem.vue';

const model = ref([]);

onMounted(async () => {
    const response = await MenuService.getMenu();
    const rawMenu = response.data;

    // 1. Megkeressük az "employees" menüpontot
    const adminSection = rawMenu[0]?.items?.find(i => i.label === 'administration');
    const employeesItem = adminSection?.items?.find(i => i.label === 'employees');

    // 2. Hozzáadjuk a két új almenüpontot, ha megtaláltuk
    if (employeesItem) {
        employeesItem.items = [
            ...(employeesItem.items || []),
            {
                label: 'Leaders',
                icon: 'pi pi-user-plus',
                to: 'http://company-02.mt/employees',
                items: []
            },
            {
                label: 'Workers',
                icon: 'pi pi-user',
                to: 'http://company-02.mt/employees',
                items: []
            }
        ];
    }

    // 3. Átalakítás saját modellre
    model.value = transformMenuToSakaiModel(rawMenu);

    console.log('model.value', model.value);
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
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(group, i) in model" :key="i">
            <AppMenuItem v-show="!item?.can || has(item.can)" v-for="(item, j) in group.items" :key="j" :item="item"
                :index="j" />
        </template>
    </ul>
</template>
