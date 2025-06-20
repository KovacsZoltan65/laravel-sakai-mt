<script setup>
    import { onMounted, reactive, watch } from "vue";
    import AppLayout from "@/sakai/layout/AppLayout.vue";
    import { Head } from "@inertiajs/vue3";

    import CompanyService from "@/services/Company/CompanyService.js";

    import CreateModal from "@/Pages/Tenants/Companies/Create.vue";
    import EditModal from "@/Pages/Tenants/Companies/Edit.vue";
    import DeleteModal from "@/Pages/Tenants/Companies/Delete.vue";

    import { usePermissions } from "@/composables/usePermissions";
    import { useDataTableFetcher } from "@/composables/useDataTableFetcher";
    const { has } = usePermissions();

    const props = defineProps({
        title: String,
        filters: Object
    });

    const fetchCompanies = async (params) => {
        try {
            const response = await CompanyService.getCompanies(params);

            return response.data;
        } catch(errors) {
            console.log(errors);
            // Opcionálisan toast, vagy hibamező frissítése:
        // toast.add({ severity: 'error', summary: 'Hiba', detail: 'Nem sikerült lekérni az adatokat' });
        }
    }

    // 👇 Hook használata
    const {
        data: companies,
        params,
        isLoading,
        fetchData,
        onPageChange,
        clearSearch
    } = useDataTableFetcher(props.filters, fetchCompanies);

    // 👇 Modálvezérlés külön
    const data = reactive({
        createOpen: false,
        editOpen: false,
        deleteOpen: false,
        company: null
    });

    //watch(() => ({ ...params }), (newParams) => {
    //    Inertia.replace(route('companies.index', newParams), { preserveScroll: true, preserveState: true });
    //}, { deep: true });

    onMounted(fetchData);

</script>

<template>

    <Head :title="props.title"/>

    <AppLayout>
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Cégek kezelése</h1>

            <!-- CREATE MODAL -->
            <CreateModal
                :show="data.createOpen"
                :title="data.title"
                @close="data.createOpen = false"
                @saved="fetchData"
            />

            <!-- EDIT MODAL -->
            <EditModal
                :show="data.editOpen"
                :title="data.title"
                :company="data.company"
                @close="data.editOpen = false"
                @saved="fetchData"
            />

            <!-- DELETE MODAL -->
            <DeleteModal
                :show="data.deleteOpen"
                :title="data.title"
                :company="data.company"
                @close="data.deleteOpen = false"
                @deleted="fetchData"
            />

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
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
            />

            <DataTable
                v-if="companies"
                :value="companies.data"
                :rows="companies.per_page"
                :totalRecords="companies.total"
                :first="(companies.current_page - 1) * companies.per_page"
                :loading="isLoading"
                lazy paginator
                dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem">

                <template #header>
                    <div class="flex justify-between">
                        <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearSearch" />
                        <div class="font-semibold text-xl mb-1">Cégek kezelése</div>
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
                <Column field="email" header="email" />
                <Column field="address" header="address" />
                <Column field="phone" header="phone" />

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">

                        <!--  UPDATE BUTTON -->
                        <Button
                            v-if="has('update company')"
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2"
                            @click="() => {
                                data.editOpen = true;
                                data.company = slotProps.data;
                            }" />

                        <!--  DELETE BUTTON -->
                        <Button
                            v-if="has('delete company')"
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
