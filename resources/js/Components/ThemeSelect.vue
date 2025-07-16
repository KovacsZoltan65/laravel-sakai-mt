<!-- ThemeSelect.vue -->
<script setup>
const model = defineModel(); // v-model wrapper (Vue 3.4+)

const themes = [
    { label: 'Automatikus', value: 'system' },
    { label: 'Világos', value: 'light' },
    { label: 'Sötét', value: 'dark' },
];
</script>

<template>
    <Select
        v-model="model"
        :options="themes"
        optionLabel="label"
        optionValue="value"
        placeholder="Téma kiválasztása"
        class="w-full"
    />
</template>
