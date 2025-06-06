<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    item: Object
});

</script>

<template>
    <div class="pl-2">
        <Link
            v-if="item.route_name"
            :href="route(item.route_name)"
            class="text-sm font-medium block hover:text-blue-700"
        >
            {{ item.name }}
        </Link>
        <span v-else class="font-semibold text-gray-700">{{ item.name }}</span>

        <div v-if="item.children && item.children.length" class="pl-4 mt-1 space-y-1 border-l border-gray-300">
            <MenuNode v-for="child in item.children" :key="child.id" :item="child" />
        </div>
    </div>
</template>