<script setup>
    import { onMounted, reactive, watch, ref } from "vue";
    import { Head, usePage } from "@inertiajs/vue3";
    import AppLayout from "@/sakai/layout/AppLayout.vue";
    // Szolgáltatás
    import CompanyService from "@/services/Company/HqCompanyService.js";

    import CreateModal from "@/Pages/Hq/Company/Create.vue";
    import EditModal from "@/Pages/Hq/Company/Edit.vue";
    import DeleteModal from "@/Pages/Hq/Company/Delete.vue";

    import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

    import TenantSelect from "@/Components/Selectors/Hq/TenantSelector.vue";

    import { usePermissions } from '@/composables/usePermissions';
    const { has } = usePermissions();

    const selectedCompanyId = ref(null);

    const props = defineProps({
        title: String,
        filters: Object
    });

    const selectedTenant = ref('');
    //const companies = ref([]);

    // 👇 API hívás definíció
    const fetchCompanies = async (params) => {
        if (!selectedTenant.value) return;

        //const response = await axios.post(route('companies.fetch'), {
        //    tenant_id: selectedTenant.value
        //});

        const response = await CompanyService.hq_getCompanies({
            ...params,
            tenant_id: selectedTenant.value
        });

        companies.value = response.data.companies;
        return response.data.companies; // 💥 EZ KELL
    }

    // 👇 Hook használata
    const {
        data: companies = {},
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

    watch(selectedTenant, (newVal) => {
        if( newVal ) {
            fetchCompanies();
        }
    });

</script>

<template>

    <Head :title="props.title"/>

    <AppLayout>
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Cégek kezelése</h1>

            <!--  CREATE MODAL -->
            <CreateModal
                :show="data.createOpen"
                :title="props.title"
                :tenantId="selectedTenant"
                @close="data.createOpen = false"
                @saved="fetchCompanies"
            />
            <EditModal
                :show="data.editOpen"
                :company="data.company"
                :title="props.title"
                :tenantId="selectedTenant"
                @close="data.editOpen = false"
                @saved="fetchCompanies"
            />
            <DeleteModal
                :show="data.deleteOpen"
                :company="data.company"
                :title="props.title"
                :tenantId="selectedTenant"
                @close="data.deleteOpen = false"
                @deleted="fetchCompanies"
            />

            <div class="flex flex-wrap items-center gap-2 mb-3">

                <!-- TENANT SELECTOR -->
                <TenantSelect
                    v-model="selectedTenant"
                    placeholder="Válassz..."
                />

                <!-- CREATE BUTTON -->
                <Button
                    v-if="has('create company')"
                    icon="pi pi-plus"
                    label="Create" @click="data.createOpen = true"
                    class="mr-2"
                    :disabled="!selectedTenant"
                />

                <!-- REFRESH BUTTON -->
                <Button
                    @click="fetchCompanies"
                    :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'"
                    :disabled="!selectedTenant"
                />
            </div>

            <DataTable
                v-if="companies"
                :value="companies.data"
                :rows="companies.per_page"
                :totalRecords="companies.total"
                :first="(companies.current_page - 1) * companies.per_page"
                :loading="isLoading" lazy paginator
                dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem">

                <template #header>
                    <div class="flex justify-between">

                        <!-- FILTER CLEAR -->
                        <Button
                            type="button"
                            icon="pi pi-filter-slash"
                            label="Clear" outlined
                            @click="clearSearch" />

                        <!-- TITLE -->
                        <div class="font-semibold text-xl mb-1">companies_title</div>

                        <!-- SEARCH -->
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

                <Column field="id" header="#" />
                <Column field="name" header="Name" />
                <Column field="email" header="Email" />
                <Column field="position" header="Position" />
                <!--<Column field="created_at" header="Created" />
                <Column field="updated_at" header="Updated" />-->
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button
                            v-if="has('update company')"
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2"
                            @click="() => {
                                data.editOpen = true;
                                data.company = slotProps.data;
                            }" />
                        <Button
                            v-if="has('delete company')"
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            @click="() => { data.deleteOpen = true; data.company = slotProps.data; }" />
                    </template>
                </Column>

            </DataTable>

        </div>
    </AppLayout>
</template>
