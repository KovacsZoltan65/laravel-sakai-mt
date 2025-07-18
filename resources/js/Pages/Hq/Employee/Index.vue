<script setup>
    import { onMounted, reactive, watch, ref, computed } from "vue";
    import { Head } from "@inertiajs/vue3";
    import AppLayout from "@/sakai/layout/AppLayout.vue";
    // Szolgáltatás
    import EmployeeService from "@/services/Employee/HqEmployeeService.js";

    import CreateModal from "@/Pages/Hq/Employee/Create.vue";
    import EditModal from "@/Pages/Hq/Employee/Edit.vue";
    import DeleteModal from "@/Pages/Hq/Employee/Delete.vue";

    import { useDataTableFetcher } from '@/composables/useDataTableFetcher';

    import TenantSelect from "@/Components/Selectors/Hq/TenantSelector.vue";
    import CompanySelect from "@/Components/Selectors/Hq/CompanySelector.vue";

    import { usePermissions } from '@/composables/usePermissions';
    const { has } = usePermissions();

    const selectedTenantId = ref(null);
    const selectedCompanyId = ref(null);

    const props = defineProps({
        title: String,
        filters: Object
    });

    const selectedTenant = ref('');
    const selectedCompany = ref('');
    //const employees = ref([]);

    // 👇 API hívás definíció
    const fetchEmployees = async (params) => {
        if (!selectedTenant.value || !selectedCompany.value) return;

        //const response = await axios.post(route('employees.fetch'), {
        //    tenant_id: selectedTenant.value
        //});

        const response = await EmployeeService.hq_getEmployees({
            ...params,
            tenant_id: selectedTenant.value,
            company_id: selectedCompany.value
        });

        employees.value = response.data.employees;
        return response.data.employees; // 💥 EZ KELL
    }

    // 👇 Hook használata
    const {
        data: employees = {},
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
        employee: null
    });

    watch(selectedTenant, () => {
        selectedCompany.value = null;
        employees.value = null; // vagy null, ha úgy kezeled
    });

    watch([selectedTenant, selectedCompany], ([tenant, comapny]) => {
        if( tenant && comapny ) {
            fetchEmployees();
        }
    });

    const canInteract = computed(() => {
        return !!selectedTenant.value && !!selectedCompany.value;
    });
</script>

<template>

    <Head :title="props.title"/>

    <AppLayout>
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Céges dolgozók kezelése</h1>

            <!--  CREATE MODAL -->
            <CreateModal :show="data.createOpen" :title="props.title" :tenantId="selectedTenant" :companyId="selectedCompany" @close="data.createOpen = false" @saved="fetchData" />
            <!-- EDIT MODAL -->
            <EditModal
                :show="data.editOpen"
                :employee="data.employee"
                :title="props.title"
                :tenantId="selectedTenant"
                :companyId="selectedCompany"
                @close="data.editOpen = false"
                @saved="fetchData"
            />
            <!-- DELETE MODAL -->
            <DeleteModal :show="data.deleteOpen" :employee="data.employee" :title="props.title" :tenantId="selectedTenant" @close="props.deleteOpen = false" @deleted="fetchData" />

            <div class="flex flex-wrap items-center gap-2 mb-3">
                <!-- PÉLDÁNY VÁLASZTÓ -->
                <TenantSelect v-model="selectedTenant" placeholder="Válassz..." />

                <!-- CÉG VÁLASZTÓ -->
                <CompanySelect v-model="selectedCompany" :tenantId="selectedTenant" placeholder="Válassz céget..." />

                <!-- CREATE BUTTON -->
                <Button v-if="has('create employee')" icon="pi pi-plus" label="Create" @click="data.createOpen = true" :disabled="!canInteract" />

                <!-- REFRESH BUTTON -->
                <Button @click="fetchEmployees" :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-refresh'" :disabled="!canInteract" />

            </div>

            <DataTable
                v-if="employees"
                :value="employees.data"
                :rows="employees.per_page"
                :totalRecords="employees.total"
                :first="(employees.current_page - 1) * employees.per_page"
                :loading="isLoading" lazy paginator
                dataKey="id"
                @page="onPageChange"
                tableStyle="min-width: 50rem">

                <template #header>
                    <div class="flex justify-between items-center gap-2">
                        <Button
                            type="button"
                            icon="pi pi-filter-slash"
                            label="Clear"
                            outlined
                            @click="clearSearch"
                        />
                        <div class="font-semibold text-xl mb-1">employees_title</div>

                        <!-- KERESŐMEZŐ TÖRLÉSSEL -->
                        <div class="flex items-center relative">
                            <IconField class="w-full">
                                <InputIcon><i class="pi pi-search" /></InputIcon>
                                <InputText
                                    v-model="params.search"
                                    placeholder="Keresés"
                                    class="pr-8"
                                />
                            </IconField>

                            <!-- Clear ikon jobbra, ha van szöveg -->
                            <i
                                v-if="params.search"
                                class="pi pi-times text-gray-500 absolute right-2 cursor-pointer"
                                @click="params.search = ''"
                            />
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
                            v-if="has('update employee')"
                            icon="pi pi-pencil"
                            outlined rounded
                            class="mr-2"
                            @click="() => {
                                data.editOpen = true;
                                data.employee = slotProps.data;
                            }" />
                        <Button
                            v-if="has('delete employee')"
                            icon="pi pi-trash"
                            outlined rounded
                            severity="danger"
                            @click="() => { data.deleteOpen = true; data.employee = slotProps.data; }" />
                    </template>
                </Column>

            </DataTable>

        </div>
    </AppLayout>
</template>
