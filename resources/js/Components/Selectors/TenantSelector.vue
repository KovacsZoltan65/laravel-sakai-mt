<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// props
const props = defineProps({
    filter: {
        type: Boolean,
        default: null
    }
});

const emit = defineEmits(['update:modelValue']);

const selectedTenant = ref(null);
const tenants = ref([]);
const isLoading = ref(false);

// Automatikus filter logika
const shouldUseFilter = computed(() => {
    if (props.filter !== null) return props.filter;
    return tenants.value.length > 10;
});

const emitSelection = () => {
  emit('update:modelValue', selectedTenant.value);
};

onMounted(async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/hq/tenants');
        tenants.value = response.data;
    } catch (err) {
        console.error('Nem sikerült a példányok lekérdezése:', err);
    } finally {
        isLoading.value = false;
    }
});
</script>

<template>
    <div>
        <Select
            v-model="selectedTenant"
            :options="tenants"
            optionLabel="name"
            optionValue="id"
            placeholder="Válassz példányt"
            @change="emitSelection"
            :loading="isLoading"
            class="mr-2"
            :filter="shouldUseFilter"
        />
    </div>
</template>
