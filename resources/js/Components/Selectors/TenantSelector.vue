<script setup>
    import { ref, onMounted, computed, watch } from 'vue';
    import axios from 'axios';

    // props
    const props = defineProps({
        modelValue: [String, Number, Object, null],
        filter: {
            type: Boolean,
            default: null
        }
    });

    const emit = defineEmits(['update:modelValue']);

    const selectedTenant = ref(null);
    const tenants = ref([]);
    const isLoading = ref(false);

    // Szinkronizálás kívülről befelé
    watch(() => props.modelValue, (val) => {
        selectedTenant.value = val;
    });

    // Kifelé emitálás
    watch(selectedTenant, (val) => {
        emit('update:modelValue', val);
    });

    // Automatikus vagy kézi filter vezérlés
    const shouldUseFilter = computed(() => {
        if (props.filter === true) return true;
        if (props.filter === false) return false;
        return tenants.value.length > 10;
    });

    // Tenantok betöltése
    onMounted(async () => {
        isLoading.value = true;
        try {
            const response = await axios.get('/api/hq/tenants');
            tenants.value = response.data;

            // ⚡ Automatikus kiválasztás, ha csak 1 tenant van
            if (tenants.value.length === 1) {
                selectedTenant.value = tenants.value[0].id;
            }

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
            :loading="isLoading"
            class="mr-2"
            :filter="shouldUseFilter"
        />
    </div>
</template>
