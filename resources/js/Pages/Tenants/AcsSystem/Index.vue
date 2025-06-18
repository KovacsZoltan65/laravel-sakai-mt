<script setup>
import { onMounted, reactive, watch } from "vue";
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { Head } from "@inertiajs/vue3";

import AcsService from "@/services/AcsSystem/AcsSystemService.js";

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';
const { has } = usePermissions();

import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";
import DeleteModal from "./Delete.vue";

const props = defineProps({
    title: String,
    filters: Object,
});

// 👇 API hívás definíció
const fetchAcs = async (params) => {
    try {
        const response = await AcsService.getAcsSystems(params);

        return response.data;
    } catch( error ) {
        console.log(error);
    }
    /*
    if (!selectedTenant.value) return;

    const response = await AcsService.getAcsSystems({
        tenant_id: selectedTenant.value
    });
    acs_systems.value = response.data.acs_systems;
    */
};

// 👇 Hook használata
const {
    data: acs_systems,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchAcs);

// 👇 Modálvezérlés külön
const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    acs_system: null
});

watch(() => ({ ...params }), (newParams) => {
    Inertia.replace(route('acs_systems.index', newParams), { preserveScroll: true, preserveState: true });
}, { deep: true });

onMounted(fetchData);

</script>

<template>

    <Head :title="props.title"/>

    <AppLayout>
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Beléptető rendszerek kezelése</h1>

            <!-- CREATE -->
            <CreateModal
                :show="data.createOpen"
                :title="props.title"
                @close="data.createOpen = false"
                @saved="fetchData"
            />

            <!-- UPDATE -->
            <EditModal
                :show="data.editOpen"
                :employee="data.acs_system"
                :title="props.title"
                @close="data.editOpen = false"
                @saved="fetchData"
            />

            <!-- DELETE -->
            <DeleteModal
                :show="data.deleteOpen"
                :acs_system="data.acs_system"
                :title="props.title"
                @close="data.deleteOpen = false"
                @deleted="fetchData" />

            <!-- CREATE BUTTON -->
            <Button
                v-if="has('create acs_system')"
                icon="pi pi-plus"
                label="Create"
                @click="data.createOpen = true"
                class="mr-2" />

            <!-- REFRESH BUTTON -->
            <Button
                @click="fetchData"
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
            />

            <DataTable
                v-if="acs_systems"
                :value="acs_systems.data"
                :rows="acs_systems.per_page"
                :totalRecords="acs_systems.total"
                :first="(acs_systems.current_page - 1) * acs_systems.per_page"
                :loading="isLoading" lazy paginator dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem">

                <template #header></template>

                <template #empty>No data found.</template>

                <template #loading>Loading data. Please wait.</template>

                <Column field="name" header="Name" />

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">

                        <!--  UPDATE BUTTON -->
                        <Button
                            v-if="has('update acs_system')"
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2"
                            @click="() => {
                                data.editOpen = true;
                                data.acs_system = slotProps.data;
                            }" />

                        <!--  DELETE BUTTON -->
                        <Button
                            v-if="has('delete acs_system')"
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            @click="() => {
                                data.deleteOpen = true;
                                data.acs_system = slotProps.data;
                            }" />

                    </template>
                </Column>

            </DataTable>

        </div>
    </AppLayout>

</template>
