<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const emit = defineEmits(['update:modelValue']);

const selectedCompany = ref(null);
const companies = ref([]);
const loading = ref(false);

const emitSelection = () => {
  emit('update:modelValue', selectedCompany.value);
};

onMounted(async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/hq/companies');
    companies.value = response.data;
  } catch (err) {
    console.error('Nem sikerült a cégek lekérdezése:', err);
  } finally {
    loading.value = false;
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
            @change="emitSelection"
            :loading="isLoading"
            class="mr-2" filter="false"
        />
    </div>
</template>
