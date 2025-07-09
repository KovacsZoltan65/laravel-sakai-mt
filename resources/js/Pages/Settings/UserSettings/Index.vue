<script setup>
import { onMounted, reactive, watch } from 'vue';
import AppLayout from '@/sakai/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';

import UserSettingService from '@/services/Settings/UserSettingsService.js';
import { usePermissions } from '@/composables/usePermissions';
import { useDataTableFetcher } from '@/composables/useDataTableFetcher';
import CreateModal from './Create.vue';
import EditModal from './Edit.vue';
import DeleteModal from './Delete.vue';

const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object
});

const {
    data: settings,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, UserSettingService.getSettings);

const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    setting: null
});

watch(() => ({ ...params }), (newParams) => {
    Inertia.replace(route('settings.user.index', newParams), { preserveScroll: true, preserveState: true });
}, { deep: true });

onMounted(fetchData);
</script>

<template>

    <Head :title="props.title" />
    <AppLayout>
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Felhasználói beállítások</h1>

            <CreateModal :show="data.createOpen" @close="data.createOpen = false" @saved="fetchData" />

            <EditModal :show="data.editOpen" :setting="data.setting" @close="data.editOpen = false"
                @saved="fetchData" />

            <DeleteModal :show="data.deleteOpen" @close="data.deleteOpen = false" @deleted="fetchData" />

            <div class="mb-4 flex gap-2 items-center">
                <Button icon="pi pi-plus" label="Új" @click="data.createOpen = true"
                    v-if="has('create user_setting')" />
                <Button :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" @click="fetchData" />
                <Button icon="pi pi-filter-slash" label="Törlés" outlined @click="clearSearch" />
                <span class="ml-auto">
                    <IconField>
                        <InputIcon><i class="pi pi-search" /></InputIcon>
                        <InputText v-model="params.search" placeholder="Keresés..." />
                    </IconField>
                </span>
            </div>

            <DataTable v-if="settings" :value="settings.data" :rows="settings.per_page" :totalRecords="settings.total"
                :first="(settings.current_page - 1) * settings.per_page" :loading="isLoading" lazy paginator
                dataKey="id" @page="onPageChange" tableStyle="min-width: 50rem">
                <template #empty>Nincs adat.</template>
                <template #loading>Betöltés...</template>

                <Column field="key" header="Kulcs" />
                <Column field="value" header="Érték" />
                <Column field="type" header="Típus" />

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined class="mr-2" v-if="has('update user_setting')"
                            @click="() => { data.editOpen = true; data.setting = slotProps.data }" />

                        <Button icon="pi pi-trash" outlined severity="danger" v-if="has('delete user_setting')"
                            @click="() => { data.deleteOpen = true; data.setting = slotProps.data }" />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>
</template>
