<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

import MenuService from '@/services/Menu/MenuService.js';
import MenuItemNode from './MenuItemNode.vue';
import AppLayout from '@/sakai/layout/AppLayout.vue';

const page = usePage();

const props = defineProps({
    title: String,
    filters: Object
});

const menuTree = ref([]);

const fetchMenu = async () => {
    const response = await MenuService.fetchTree();
    //menuTree.value = response.data[0].items;
    menuTree.value = response.data;
};

onMounted(fetchMenu);
</script>

<template>
    <Head :title="props.title"/>

    <AppLayout>
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Menük kezelése</h1>

            <div v-if="menuTree.length > 0">
                <MenuItemNode
                    v-for="(item, index) in menuTree"
                    :key="item.id"
                    :item="item"
                    :depth="0"
                    @refresh="fetchMenu"
                />
            </div>
        </div>
    </AppLayout>

</template>
