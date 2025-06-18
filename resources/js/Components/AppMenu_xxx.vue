<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import MenuNode from "./MenuNode.vue";

const menuItem = ref([]);

onMounted(async () => {
    const response = await axios.get("/api/menu-items");
    menuItem.value = response.data;
});

</script>

<template>
    <nav class="space-y-1">
        <MenuNode v-for="item in menuItems" :key="item.id" :item="item" />
    </nav>
</template>