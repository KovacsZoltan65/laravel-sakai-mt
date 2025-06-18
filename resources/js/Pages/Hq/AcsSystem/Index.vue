<script setup>
import {onMounted, reactive, ref, watch} from "vue";
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { Head } from "@inertiajs/vue3";

import AcsService from "@/services/AcsSystem/HqAcsSystemService.js";

import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';
const { has } = usePermissions();

import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";
import DeleteModal from "./Delete.vue";

const props = defineProps({
    title: String,
    filters: Object,
    tenants: Array
});

const selectedTenant = ref('');

// 👇 API hívás definíció
const fetchAcs = async () => {
    if (!selectedTenant.value) return;

    const response = await AcsService.hq_getAcsSystems({
        tenant_id: selectedTenant.value
    });
    acs_systems.value = response.data.acs_systems;
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
    acs_systems: null
});

</script>

<template>

    <Head :title="props.title"/>

    <AppLayout>
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Beléptető rendszerek kezelése</h1>

            <CreateModal />

            <EditModal />

            <DeleteModal />

            <Select
                v-model="selectedTenant"
                @change="fetchAcs"
                :options="tenants"
                optionLabel="name"
                optionValue="id"
                placeholder="Válasst ..."
                class="mr-2"
            />

            <Button
                v-if="has('create acs_system')"
                icon="pi pi-plus" class="mr-2"
                label="Create" @click="data.createOpen = true"
                :disabled="!selectedTenant"
            />

            <Button
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
                :disabled="!selectedTenant"
                @click="fetchAcs"
            />

            <DataTable></DataTable>

        </div>
    </AppLayout>

</template>
