<script setup>
import { onMounted, reactive, watch } from "vue";
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { Head } from "@inertiajs/vue3";

import EmployeeService from "@/services/EmployeeService.js";

import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";
import DeleteModal from "./Delete.vue";

import { usePermissions } from "@/composables/usePermissions";
import { useDataTableFetcher } from "@/composables/useDataTableFetcher";

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object
});

// 👇 API hívás definíció
const fetchEmployees = async (params) => {
    try {
        const response = await EmployeeService.getEmployees(params);

        console.log('response', response.data);

        return response.data;
    } catch( error ) {
        console.log(error);
    }
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

onMounted(fetchData);

</script>

<template>

    <Head :title="props.title"/>

    <AppLayout>
        <div class="card">

            <!-- CREATE MODAL -->
            <CreateModal
                :show="data.createOpen"
                :title="props.title"
                @close="data.createOpen = false"
                @saved="fetchData"
            />

            <!-- EDIT MODAL -->
            <EditModal
                :show="data.editOpen"
                :company="data.company"
                :title="props.title"
                @close="data.editOpen = false"
                @saved="fetchData"
            />

            <!-- DELETE MODAL -->
            <DeleteModal
                :show="data.deleteOpen"
                :company="data.company"
                :title="props.title"
                @close="data.deleteOpen = false"
                @deleted="fetchData" />

            <!-- CREATE BUTTON -->
            <Button
                v-if="has('create company')"
                icon="pi pi-plus"
                label="Create"
                @click="data.createOpen = true"
                class="mr-2" />

            <!-- REFRESH BUTTON -->
            <Button
                @click="fetchData"
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" />

            <!-- DATATABLE -->
            <DataTable v-if="employees" :value="employees.data" :rows="employees.per_page"
                :totalRecords="employees.total" :first="(employees.current_page - 1) * employees.per_page"
                :loading="isLoading" lazy paginator dataKey="id" @page="onPageChange" tableStyle="min-width: 50rem">

                <template #header></template>

                <template #empty>No data found.</template>

                <template #loading>Loading data. Please wait.</template>

                <Column field="name" header="Name" />
                <Column field="position" header="Position" />
                <Column field="email" header="Email" />
                <Column field="created_at" header="Created" />
                <Column field="updated_at" header="Updated" />

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">

                        <!--  UPDATE BUTTON -->
                        <Button
                            v-if="has('update employee')"
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2"
                            @click="() => {
                                data.editOpen = true;
                                data.company = slotProps.data;
                            }" />

                        <!--  DELETE BUTTON -->
                        <Button
                            v-if="has('delete employee')"
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            @click="() => {
                                data.deleteOpen = true;
                                data.company = slotProps.data;
                            }" />

                    </template>
                </Column>

            </DataTable>

        </div>
    </AppLayout>

</template>

<style scoped lang="scss"></style>
