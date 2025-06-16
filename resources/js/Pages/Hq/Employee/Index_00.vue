<script setup>
import { onMounted, reactive, watch, ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";

//import AuthLayout from "@/Layouts/AuthenticatedLayout.vue";
import AppLayout from "@/sakai/layout/AppLayout.vue";

import CreateModal from "@/Pages/Hq/Employee/Create.vue";
import EditModal from "@/Pages/Hq/Employee/Edit.vue";
import DeleteModal from "@/Pages/Hq/Employee/Delete.vue";

import EmployeeService from "@/services/EmployeeService";

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

const { has } = usePermissions();

const selectedTenant = ref('');

const props = defineProps({
    title: String,
    filters: Object,
    tenants: Array,
});

// 👇 API hívás definíció
const fetchEmployees = async (params) => {
    const response = await EmployeeService.hq_getEmployees({
        ...params,
        tenant_id: selectedTenant.value
    });
    console.log(response.data);
    return response.data;
};

// 👇 Hook használata
const {
    data: employees,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchEmployees);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    company: null
});

watch(() => ({ ...params }), (newParams) => {
    Inertia.replace(route('employees.index', newParams), { preserveScroll: true, preserveState: true });
}, { deep: true });

watch(selectedTenant, () => {
    fetchEmployees();
});

</script>

<template>
    <AppLayout>
        <Head :title="props.title" />

        <div class="card">

            <CreateModal
                :show="data.createOpen"
                :title="props.title"
                @close="data.createOpen = false"
                @saved="fetchData"
            />
            <EditModal
                :show="data.editOpen"
                :company="data.company"
                :title="props.title"
                @close="data.editOpen = false"
                @saved="fetchData"
            />
            <DeleteModal
                :show="data.deleteOpen"
                :company="data.company"
                :title="props.title"
                @close="data.deleteOpen = false"
                @deleted="fetchData" />

            <Select
                v-model="selectedTenant"
                :options="tenants"
                optionLabel="name"
                optionValue="id"
                :filter="true"/>

            <Button
                v-if="has('create employee')"
                icon="pi pi-plus"
                label="Create"
                @click="data.createOpen = true"
                class="mr-2" />

            <Button
                @click="fetchData"
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" />

            <DataTable v-if="employees" :value="employees.data" :rows="employees.per_page"
                :totalRecords="employees.total" :first="(employees.current_page - 1) * employees.per_page"
                :loading="isLoading" lazy paginator dataKey="id" @page="onPageChange" tableStyle="min-width: 50rem">

                <template #header>
                    <div class="flex justify-between">
                        <Button
                            type="button"
                            icon="pi pi-filter-slash"
                            label="Clear" outlined
                            @click="clearSearch"
                        />

                        <div class="font-semibold text-xl mb-1">
                            {{props.title}}
                        </div>

                        <div class="flex justify-end">
                            <IconField>
                                <InputIcon><i class="pi pi-search" /></InputIcon>
                                <InputText v-model="params.search" placeholder="Keyword Search" />
                            </IconField>
                        </div>
                    </div>
                </template>

                <template #empty>No data found.</template>
                <template #loading>Loading data. Please wait.</template>

            </DataTable>

        </div>
    </AppLayout>
</template>
