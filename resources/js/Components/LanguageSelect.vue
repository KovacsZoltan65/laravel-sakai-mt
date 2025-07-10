<script setup>
import { ref, onMounted } from 'vue';

const model = defineModel(); // vagy defineProps / defineEmits

const options = ref([]);
const availableLocales = window?.available_locales || [];
const supportedLocales = window?.supported_locales || [];

onMounted(() => {

    options.value = availableLocales
        .filter(locale => supportedLocales.includes(locale.code.toLowerCase()))
        .map(locale => ({
            label: locale.name,
            value: locale.code.toLowerCase()
        }));

});
</script>

<template>
    <Select
        v-model="model"
        :options="options"
        optionLabel="label"
        optionValue="value"
        class="w-full"
        placeholder="Válassz nyelvet"
    />
</template>
