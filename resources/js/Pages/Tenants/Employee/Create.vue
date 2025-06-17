<script setup>
import { ref, computed } from "vue";

import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import EmployeeService from "@/services/Employee/EmployeeService.js";

const props = defineProps({
    show: Boolean,
    title: String,
});

const emit = defineEmits(["close", "saved"]);

const isSaving = ref(false);

// Form adatok
const form = ref({
    name: '',
    email: '',
    position: '',
    // ide jön minden egyéb mező
});

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    email: { required, email },
    position: { required, minLength: minLength(3), maxLength: maxLength(255) }
}));

const v$ = useVuelidate(rules, form);

const save = async () => {
    isSaving.value = true;

    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            await EmployeeService.storeEmployee({
                ...form.value,
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

            <!-- POSITION -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="position" class="block font-bold mb-3">
                        position
                    </label>
                    <InputText
                        id="position"
                        v-model="form.position"
                        fluid
                    />
                </FloatLabel>
                <!--<Message
                    size="small"
                    severity="secondary"
                    variant="simple"
                >
                    enter_position
                </Message>-->
                <small class="text-red-500" v-if="v$.position.$error">
                    {{ v$.position.$errors[0].$message }}
                </small>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" severity="secondary" @click="closeModal" />
                <Button label="Save" icon="pi pi-check" @click="save" />
            </div>

        </div>

    </Dialog>
</template>
