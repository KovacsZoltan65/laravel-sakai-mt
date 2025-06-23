<script setup>
import { ref, computed, watch } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import CompanyService from "@/services/Company/HqCompanyService.js";
import { getBools } from "@/helpers/functions.js";

const props = defineProps({
    show: Boolean,
    title: String,
    company: Object, // A szerkesztendő entitás adatai
    tenantId: [Number, String],
});

const emit = defineEmits(['close', 'saved']);

// Űrlap adatok
const form = ref({
    name: '',
    email: '',
    address: '',
    phone: '',
    active: true,
    // ide jön minden egyéb mező
});

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    email: { required, email },
    address: { required, minLength: minLength(3), maxLength: maxLength(255) },
    phone: { required },
    active: { required }
}));

const v$ = useVuelidate(rules, form);

// Amikor a props.company változik, töltsük be a form értékeit
watch(
    () => props.company,
    (newCompany) => {
        if (newCompany) {
            form.value = {
                name: newCompany.name || '',
                email: newCompany.email || '',
                address: newCompany.address || '',
                phone: newCompany.phone || '',
                active: newCompany.active || true,
            };
            v$.value.$reset(); // Reseteljük a validációt, hogy ne legyenek előző hibák
        }
    },
    { immediate: true }
);

// Frissítés (update) művelet
const updateCompany = async () => {
    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            // A szerkesztett entitás azonosítóját props.company.id használjuk
            await CompanyService.hq_updateCompany(
                props.company.id,
                {
                    ...form.value,
                    tenant_id: props.tenantId
                }
            );
            emit('saved', form.value);
            closeModal();
        } catch (e) {
            console.error('Frissítés sikertelen', e);
        }
    }
};

// Modál bezárása: reseteljük a validációs állapotot, majd emitáljuk a close eseményt
const closeModal = () => {
    v$.value.$reset();
    emit('close');
};

//const getBools = () => {
//    return [
//        { label: "NO", value: 0, },
//        { label: "YES", value: 1, },
//    ];
//};

</script>

<template>
    <Dialog
        :visible="show" modal
        header="Edit company"
        @hide="closeModal"
        :style="{ width: '550px' }"
    >
        <div class="flex flex-col gap-6" style="margin-top: 17px;">

            <!-- NAME -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="name" class="block font-bold mb-3">
                        Name
                    </label>
                    <InputText
                        id="name"
                        v-model="form.name"
                        fluid
                    />
                </FloatLabel>
                <!--<Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_company_name
                </Message>-->
                <small class="text-red-500" v-if="v$.name.$error">
                    {{ v$.name.$errors[0].$message }}
                </small>
            </div>

            <!-- EMAIL -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="email" class="block font-bold mb-3">
                        Email
                    </label>
                    <InputText
                        id="email"
                        v-model="form.email"
                        fluid
                    />
                </FloatLabel>
                <!--<Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_company_email
                </Message>-->
                <small class="text-red-500" v-if="v$.email.$error">
                    {{ v$.email.$errors[0].$message }}
                </small>
            </div>

            <!-- ACCRESS -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="address" class="block font-bold mb-3">
                        Address
                    </label>
                    <InputText
                        id="address"
                        v-model="form.address"
                        fluid
                    />
                </FloatLabel>
                <!--<Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_company_email
                </Message>-->
                <small class="text-red-500" v-if="v$.address.$error">
                    {{ v$.address.$errors[0].$message }}
                </small>
            </div>

            <!-- PHONE -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="phone" class="block font-bold mb-3">
                        Phone
                    </label>
                    <InputText
                        id="phone"
                        v-model="form.phone"
                        fluid
                    />
                </FloatLabel>
                <!--<Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_company_email
                </Message>-->
                <small class="text-red-500" v-if="v$.phone.$error">
                    {{ v$.phone.$errors[0].$message }}
                </small>
            </div>

            <!-- ACTIVE -->
            <div class="flex flex-col grow basis-0 gap-2">
                <label for="active">Active</label>
                <!--<InputSwitch id="active" v-model="form.active" />-->
                <Select
                    id="active"
                    v-model="form.active"
                    :options="getBools()"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Select active"
                    fluid
                />
            </div>

            <!-- Gombok -->
            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" severity="secondary" @click="closeModal" />
                <Button label="Update" icon="pi pi-check" @click="updateCompany" />
            </div>

        </div>
    </Dialog>
</template>
