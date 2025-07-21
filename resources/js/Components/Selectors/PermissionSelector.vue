<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const model = defineModel();

const permissionOptions = computed(() =>
    page.props.auth?.user?.roles?.[0]?.permissions?.map(p => p.name) ?? []
);
</script>

<template>
    <Select
        v-model="model"
        :options="permissionOptions"
        placeholder="Jogosultság kiválasztása"
        class="w-full"
        filter
    />
</template>
