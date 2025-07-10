<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const model = defineModel(); // v-model wrapper (Vue 3.4+)
const timezones = ref([]);
const loading = ref(false);

onMounted(async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('timezone.list'));
        //console.log('response.data', response.data);
        //timezones.value = response.data;
        // 🔧 Átalakítás: object → array of { label, value }
        timezones.value = Object.values(response.data).map(tz => ({
            label: tz,
            value: tz
        }));
    } catch (e) {
        console.error('Időzóna lista betöltési hiba:', e);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <Select
        v-model="model"
        :options="timezones"
        optionLabel="label"
        optionValue="value"
        :loading="loading"
        placeholder="Válassz időzónát"
        class="w-full"
        :virtualScrollerOptions="{ itemSize: 42 }"
    />
</template>
