<script setup>
import { onMounted, reactive, watch } from 'vue';
import AppLayout from '@/sakai/layout/AppLayout.vue';

import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import { useDebounceFn } from '@vueuse/core';

import CompanySettingsService from '@/services/Settings/CompanySettingsService';
import PreviewValue from "@/Components/PreviewValue.vue";
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

const fetchSettings = async (params) => {
    try {
        const response = await CompanySettingsService.getSettings(params);
        return response.data;
        //return await UserSettingService.getSettings(params);
    } catch( errors ) {
        console.log(errors);
    }
};

const {
    data: settings,
    params,
    isLoading,
    fetchData,
    onPageChange,
    clearSearch
} = useDataTableFetcher(props.filters, fetchSettings);

const data = reactive({
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    setting: null
});

watch(() => ({ ...params }), (newParams) => {
    Inertia.replace(route('comp_settings.index', newParams), { preserveScroll: true, preserveState: true });
}, { deep: true });

/**
 * A keresési funkció visszavonása a túl sok kérés elkerülése érdekében.
 * @param {function} fn a visszapattanás-csökkentő függvény
 * @param {number} wait a pattanáscsillapítási idő milliszekundumban
 * @returns {function} a visszapattanásmentesített függvény
 */
const onSearch = useDebounceFn((fn) => {
    fetchData();
}, 500);

onMounted(fetchData);
</script>

<template>

    <Head :title="props.title" />
    <AppLayout>
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Felhasználói beállítások</h1>

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
                :setting="data.setting"
                :title="props.title"
                @close="data.editOpen = false"
                @saved="fetchData"
            />

            <!-- DELETE MODAL -->
            <DeleteModal
                :show="data.deleteOpen"
                :setting="data.setting"
                :title="props.title"
                @close="data.deleteOpen = false"
                @deleted="fetchData"
            />

            <!-- CREATE BUTTON -->
            <Button
                icon="pi pi-plus"
                label="Új"
                @click="data.createOpen = true"
                v-if="has('create user_setting')"
                class="mr-2"
            />

            <!-- REFRESH BUTTON -->
            <Button
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
                @click="fetchData"
            />

            <DataTable
                v-if="settings"
                :value="settings.data"
                :rows="settings.per_page"
                :totalRecords="settings.total"
                :first="(settings.current_page - 1) * settings.per_page"
                :loading="isLoading"
                lazy paginator
                dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem"
            >

                <template #header>
                    <div class="flex justify-between">

                        <!-- TÖRLÉS -->
                        <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined
                            @click="clearSearch" />

                        <!-- FELIRAT -->
                        <div class="font-semibold text-xl mb-1">app_settings_title</div>

                        <!-- KERESÉS -->
                        <div class="flex justify-end">
                            <IconField>
                                <InputIcon><i class="pi pi-search" /></InputIcon>
                                <InputText v-model="params.search" @input="onSearch" placeholder="Keresés..." />
                            </IconField>
                        </div>
                    </div>
                </template>

                <template #empty>Nincs adat.</template>
                <template #loading>Betöltés...</template>

                <Column field="key" header="Kulcs" />

                <Column header="Value">
                    <template #body="slotProps">
                        <PreviewValue
                            :value="slotProps.data.value"
                            :type="slotProps.data.type"
                        />
                    </template>
                </Column>

                <Column field="type" header="Type">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.type"
                            :severity="slotProps.data.type === 'bool' ? 'success' : slotProps.data.type === 'int' ? 'info' : 'warning'" />
                    </template>
                </Column>

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <!-- EDIT BUTTON -->
                        <Button
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2" v-if="has('update user_setting')"
                            @click="() => {
                                data.editOpen = true;
                                data.setting = slotProps.data
                            }"
                        />

                        <!-- DELETE BUTTON -->
                        <Button
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            v-if="has('delete user_setting')"
                            @click="() => {
                                data.deleteOpen = true;
                                data.setting = slotProps.data
                            }"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>
</template>
