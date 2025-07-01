<script setup>
import { onMounted, reactive, watch } from "vue";
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Inertia } from '@inertiajs/inertia';

import TenantService from "@/services/Tenant/TenantService.js";

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
const fetchTenants = async (params) => {
    try {
        const response = await TenantService.getTenants(params);

        return response.data;
    } catch( errors ) {
        console.log(errors);
    }
};

// 👇 Hook használata
const {
    data: tenants,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchTenants);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    tenant: null
});

watch(() => ({ ...params }), (newParams) => {
    Inertia.replace(route('tenants.index', newParams), { preserveScroll: true, preserveState: true });
}, { deep: true });

onMounted(fetchData);

</script>

<template>

    <Head :title="props.title"/>

    <AppLayout>
        {{ data.editOpen }}
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Céges dolgozók kezelése</h1>

            <!-- CREATE MODAL -->
            <CreateModal :show="data.createOpen" :title="props.title" @close="data.createOpen = false" @saved="fetchData" />
            <!-- EDIT MODAL -->
            <EditModal
                :show="data.editOpen"
                :tenant="data.tenant"
                :title="props.title"
                @close="data.editOpen = false"
                @updated="fetchData" />
            <!-- DELETE MODAL -->
            <DeleteModal :show="data.deleteOpen" :tenant="data.tenant" :title="props.title" @close="data.deleteOpen = false" @deleted="fetchData" />

            <!-- CREATE BUTTON -->
            <Button v-if="has('create tenant')" icon="pi pi-plus" label="Create" @click="data.createOpen = true" class="mr-2" />
            <!-- REFRESH BUTTON -->
            <Button @click="fetchData" :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" />

            <!-- DATATABLE -->
            <DataTable
                v-if="tenants"
                :value="tenants.data"
                :rows="tenants.per_page"
                :totalRecords="tenants.total"
                :first="(tenants.current_page - 1) * tenants.per_page"
                :loading="isLoading"
                lazy paginator
                dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem">

                <template #header>
                    <div class="flex justify-between">

                        <!-- TÖRLÉS -->
                        <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearSearch" />

                        <!-- FELIRAT -->
                        <div class="font-semibold text-xl mb-1">tenants_title</div>

                        <!-- KERESÉS -->
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

                <Column field="name" header="Name" />
                <Column field="domain" header="Domain" />
                <Column field="database" header="database" />

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">

                        <!--  UPDATE BUTTON -->
                        <Button
                            v-if="has('update tenant')"
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2"
                            @click="() => {
                                data.tenant = slotProps.data;
                                data.editOpen = true;
                            }" />

                        <!--  DELETE BUTTON -->
                        <Button
                            v-if="has('delete tenant')"
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            @click="() => {
                                data.tenant = slotProps.data;
                                data.deleteOpen = true;
                            }" />

                    </template>
                </Column>

            </DataTable>

        </div>
    </AppLayout>

</template>

<style scoped lang="scss"></style>
