<script setup>
    import { ref, onMounted, computed, watch } from 'vue';
    import axios from 'axios';

    // Props
    const props = defineProps({
        modelValue: [String, Number, Object, null], // a v-model értéke
        tenantId: {
            type: [String, Number],
            required: false
        },
        filter: {
            type: Boolean,
            default: null // ha nincs megadva, akkor automatikus mód lép életbe
        },
        disabled: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits(['update:modelValue']);

    const selectedCompany = ref(props.modelValue);
    const companies = ref([]);
    const isLoading = ref(false);

    // Szinkronizálás kívülről befelé (v-model -> helyi érték)
    watch(() => props.modelValue, (val) => {
        selectedCompany.value = val;
    });

    // Változás emitálása (helyi érték -> parent)
    watch(selectedCompany, (val) => {
        emit('update:modelValue', val);
    });

    // Automatikus vagy kézi filter logika
    const shouldUseFilter = computed(() => {
        if (props.filter === true) return true;
        if (props.filter === false) return false;
        return companies.value.length > 10;
    });

    const emitSelection = () => {
        emit('update:modelValue', selectedCompany.value);
    };

    // Tenant váltásra cégek újratöltése
    watch(() => props.tenantId, async (newTenantId) => {
        if (!newTenantId) {
            companies.value = [];
            selectedCompany.value = null;
            emit('update:modelValue', null);
            return;
        }

        isLoading.value = true;
        try {
            const response = await axios.get('/api/hq/companies', {
                params: {
                    tenant_id: newTenantId
                }
            });
            companies.value = response.data;

            // ⚡ automatikus kiválasztás, ha csak egy cég van
            if (companies.value.length === 1) {
                selectedCompany.value = companies.value[0].id;
            }
        } catch (error) {
            console.error('Nem sikerült a cégek lekérdezése:', error);
        } finally {
            isLoading.value = false;
        }
    });
</script>

<template>
    <div>
        <Select
            v-model="selectedCompany"
            :options="companies"
            optionLabel="name"
            optionValue="id"
            placeholder="Válassz céget"
            :loading="isLoading"
            class="mr-2"
            :filter="shouldUseFilter"
            :disabled="props.disabled"
        />
    </div>
</template>
