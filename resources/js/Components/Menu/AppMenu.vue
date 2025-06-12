<script setup>
import { onMounted, ref } from "vue";
import AppMenuItem from "@/sakai/layout/AppMenuItem.vue";
import MenuService from "@/services/MenuService";

const menuItems = ref();

const fetchItems = () => {
    MenuService.getMenuItems()
    .then((response) => {
        menuItems.value = response.data;
    })
    .catch((error) => {
        console.error("getMenuItems API Error:", error);
    });
}

onMounted(() => {
    fetchItems();
});

</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in menuItems" :key="item">
            <AppMenuItem
                v-if="!item.separator"
                :item="item"
                :index="i"
            />

            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

