<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import MenuService from '@/services/Menu/MenuService.js';
import MenuItemNode from './MenuItemNode.vue';
import AppLayout from '@/sakai/layout/AppLayout.vue';

const props = defineProps({
    title: String,
    filters: Object
});

const menuTree = ref([]);

const fetchMenu = async () => {
    const response = await MenuService.fetchTree();
    menuTree.value = response.data[0].items;
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
<!--
<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-4">Menü adminisztráció</h2>
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
</template>
-->
