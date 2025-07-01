<script setup>
import { ref, computed, watch } from "vue";

import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import TenantService from "@/services/Tenant/HqTenantService.js";

import { getBools } from "@/helpers/functions.js";

import { useToast } from 'primevue/usetoast';
const toast = useToast();

const props = defineProps({
    show: Boolean,
    title: String,
});

const emit = defineEmits(["close", "saved"]);

const isSaving = ref(false);
const formErrors = ref({});
const nameInputRef = ref();

// 🧩 Alapértelmezett űrlapobjektum
const emptyForm = () => ({
    name: '',
    domain: '',
    host: 'localhost',
    port: 3306,
    database: '',
    username: '',
    password: '',
    active: 1
});

const form = ref(emptyForm());

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    domain: { required },
    host: { required },
    port: { required },
    database: { required },
    username: { required },
    password: { required },
}));

const v$ = useVuelidate(rules, form);

// Mentés
const save = async () => {
    isSaving.value = true;

    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            await TenantService.storeTenant({
                ...form.value
            });

            emit('saved', form.value);

            toast.add({
                severity: 'success',
                summary: 'Mentés sikeres',
                detail: `${form.value.name} elmentve`,
                life: 3000
            });

            closeModal();
        } catch (e) {
            if (e.response && e.response.data && e.response.data.errors) {
                formErrors.value = e.response.data.errors;
            } else {
                console.error('Mentés sikertelen', e);
            }
        } finally {
            isSaving.value = false;
        }
    }
};

// ❌ Bezárás és form reset
const closeModal = () => {
    v$.value.$reset();
    form.value = emptyForm();
    formErrors.value = {};
    emit('close');
};

</script>

<template>
    <Dialog
        :visible="show"
        modal :closable="false"
        :style="{ width: '550px' }"
        header="Create Employee"
        @hide="closeModal"
    >
        <div class="p-1 flex flex-col">

            <!-- Row 1: Name + Domain -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <InputText id="name" v-model="form.name" class="w-full" />
                    <p class="mt-1 text-sm text-gray-500">A teljes név, amit meg szeretnél jeleníteni.</p>
                    <small class="text-red-500" v-if="v$.name.$error">
                        {{ v$.name.$errors[0].$message }}
                    </small>
                </div>

                <div>
                    <label for="domain" class="block text-sm font-medium text-gray-700">Domain</label>
                    <InputText id="domain" v-model="form.domain" class="w-full" />
                    <p class="mt-1 text-sm text-gray-500">Pl.: <code>cegnev.local</code> vagy <code>acme.hu</code></p>
                    <small class="text-red-500" v-if="v$.domain.$error">
                        {{ v$.domain.$errors[0].$message }}
                    </small>
                </div>
            </div>

            <!-- Row 2: Host + Port -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="host" class="block text-sm font-medium text-gray-700">Host</label>
                    <InputText id="host" v-model="form.host" class="w-full" />
                    <p class="mt-1 text-sm text-gray-500">Szerver címe, pl. <code>127.0.0.1</code> vagy <code>mysql</code>.</p>
                    <small class="text-red-500" v-if="v$.host.$error">
                        {{ v$.host.$errors[0].$message }}
                    </small>
                </div>

                <div>
                    <label for="port" class="block text-sm font-medium text-gray-700">Port</label>
                    <InputText id="port" v-model="form.port" class="w-full" />
                    <p class="mt-1 text-sm text-gray-500">Csatlakozási portja, általában <code>3306</code>.</p>
                    <small class="text-red-500" v-if="v$.port.$error">
                        {{ v$.port.$errors[0].$message }}
                    </small>
                </div>
            </div>

            <!-- Database -->
            <div>
                <label for="database" class="block text-sm font-medium text-gray-700">Database</label>
                <InputText id="database" v-model="form.database" class="w-full" />
                <p class="mt-1 text-sm text-gray-500">Az adatbázis neve, pl. <code>tenant_acme</code>.</p>
                <small class="text-red-500" v-if="v$.database.$error">
                    {{ v$.database.$errors[0].$message }}
                </small>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <InputText id="username" v-model="form.username" class="w-full" />
                    <p class="mt-1 text-sm text-gray-500">Adatbázis felhasználó neve, pl. <code>tenant_user</code>.</p>
                    <small class="text-red-500" v-if="v$.username.$error">
                        {{ v$.username.$errors[0].$message }}
                    </small>
                </div>

                <!-- Password -->
                <div>
                    <label
                        for="password"
                        class="block text-sm font-medium text-gray-700">Password</label>

                    <Password v-model="form.password" class="w-full" inputClass="w-full"  toggleMask>
                        <template #header>
                            <div class="font-semibold text-sm mb-2">Pick a password</div>
                        </template>
                        <template #footer>
                            <Divider />
                            <ul class="pl-2 my-0 leading-normal">
                                <li>At least one lowercase</li>
                                <li>At least one uppercase</li>
                                <li>At least one numeric</li>
                                <li>Minimum 8 characters</li>
                            </ul>
                        </template>
                    </Password>

                    <p class="mt-1 text-sm text-gray-500">Adatbázis jelszó – biztonságos módon kezeld!</p>
                    <small class="text-red-500" v-if="v$.password.$error">
                        {{ v$.password.$errors[0].$message }}
                    </small>
                </div>
            </div>
            <!-- ACTIVE -->
            <div class="flex flex-col grow basis-0 gap-2">
                <label
                    for="active"
                    class="block text-sm font-medium text-gray-700"
                >Active</label>

                <Select
                    id="active"
                    v-model="form.active"
                    :options="getBools()"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Select active"
                    fluid
                />
                <p class="text-xs text-gray-500 mt-0.5">Engedélyezett példány (aktív vagy inaktív).</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-2 pt-4 border-t">
                <Button label="Cancel" severity="secondary" @click="closeModal" />
                <Button label="Save" icon="pi pi-check" :loading="isSaving" :disabled="isSaving" @click="save" />
            </div>

        </div>
    </Dialog>
</template>

