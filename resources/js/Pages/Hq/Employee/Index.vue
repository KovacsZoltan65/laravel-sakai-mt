<script setup>
import { onMounted, reactive, watch, ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import AppLayout from "@/sakai/layout/AppLayout.vue";

const props = defineProps({
    title: String,
    filters: Object,
    tenants: Array
});

const selectedTenant = ref('');
const employees = ref([]);

const loadEmployees = async () => {
    if (!selectedTenant.value) return;

    const response = await axios.post(route('employees.fetch'), {
        tenant_id: selectedTenant.value
    });
    employees.value = response.data.employees;
}

</script>

<template>

    <Head :title="props.title"/>

    <AppLayout>
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Céges dolgozók kezelése</h1>

            <select 
                v-model="selectedTenant" 
                @change="loadEmployees" 
                class="border rounded px-3 py-1 mb-4">
                <option disabled value="">Válassz céget</option>
                <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                    {{ tenant.name }}
                </option>
            </select>

            {{ employees }}
        </div>
    </AppLayout>
</template>
