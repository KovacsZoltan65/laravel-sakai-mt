<script setup>
import { ref, computed } from "vue";

import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import CompanyService from "@/services/Company/HqCompanyService.js";

import { getBools } from "@/helpers/functions.js";

const props = defineProps({
    show: Boolean,
    title: String,
    tenantId: [Number, String],
});

const emit = defineEmits(["close", "saved"]);

const isSaving = ref(false);
const formErrors = ref({});

// Form adatok
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

// Mentés
const save = async () => {
    isSaving.value = true;

    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            await CompanyService.hq_storeCompany({
                ...form.value,
                tenant_id: props.tenantId
            });

            emit('saved', form.value);
            closeModal();
        } catch (e) {
            console.error('Mentés sikertelen', e);
        } finally {
            isSaving.value = false;
        }
    }
};

const closeModal = () => {
    v$.value.$reset(); // 👈 hibák törlése
    emit('close');
};

</script>

<template>
    <Dialog
        :visible="show"
        :style="{ width: '550px' }" modal
        header="Create Employee"
        @hide="closeModal"
    >
        <div class="flex flex-col gap-6" style="margin-top: 17px;">
            <!-- NAME -->
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

            <!-- ADDFRESS -->
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

            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" severity="secondary" @click="closeModal" />
                <Button label="Save" icon="pi pi-check" @click="save" />
            </div>

        </div>

    </Dialog>
</template>
