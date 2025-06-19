<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    modelValue: [String, Number],     // a kiválasztott tenant id
    placeholder: String               // opcionális
});

const emit = defineEmits(['update:modelValue']);

const tenants = ref([]);
const loading = ref(false);

const loadTenants = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/hq/tenants');  // vagy amit használsz

        tenants.value = response.data;
    } finally {
        loading.value = false;
    }
};

const selectedTenant = ref(null);

watch(() => props.modelValue, (newVal) => {
    selectedTenant.value = tenants.value.find(t => t.id === newVal) ?? null;
});

watch(selectedTenant, (val) => {
    emit('update:modelValue', val?.id ?? null);
});

onMounted(() => {
    loadTenants();
});

</script>

<template>
    <Select
        v-model="selectedTenant"
        :options="tenants"
        optionLabel="name"
        placeholder="Válassz céget"
        :loading="loading"
        filter
        :pt="{
            panel: { style: 'max-height: 300px' }
        }"
    />
</template>
