<script setup>
import { ref, computed, watch } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import EmployeeService from "@/services/Employee/HqEmployeeService.js";

const props = defineProps({
    show: Boolean,
    title: String,
    employee: Object, // A szerkesztendő entitás adatai
    tenantId: [Number, String],
});

const emit = defineEmits(['close', 'saved']);

// Űrlap adatok
const form = ref({
    name: '',
    email: '',
    position: '',
});

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    email: { required, email },
    position: { required, minLength: minLength(3), maxLength: maxLength(255) },
}));

const v$ = useVuelidate(rules, form);

// Amikor a props.employee változik, töltsük be a form értékeit
watch(
    () => props.employee,
    (newEmployee) => {
        if (newEmployee) {
            form.value = {
                name: newEmployee.name || '',
                email: newEmployee.email || '',
                position: newEmployee.position || '',
            };
            v$.value.$reset(); // Reseteljük a validációt, hogy ne legyenek előző hibák
        }
    },
    { immediate: true }
);

// Frissítés (update) művelet
const updateEmployee = async () => {
    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            // A szerkesztett entitás azonosítóját props.employee.id használjuk
            await EmployeeService.hq_updateEmployee(
                props.employee.id,
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

</script>

<template>
    <Dialog
        :visible="show" modal
        header="Edit employee"
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
                    enter_employee_name
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
                    enter_employee_email
                </Message>-->
                <small class="text-red-500" v-if="v$.email.$error">
                    {{ v$.email.$errors[0].$message }}
                </small>
            </div>

            <!-- POSITION -->
            <div class="flex flex-col grow basis-0 gap-2">
                <FloatLabel variant="on">
                    <label for="position" class="block font-bold mb-3">
                        Position
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
                    enter_employee_position
                </Message>-->
                <small class="text-red-500" v-if="v$.position.$error">
                    {{ v$.position.$errors[0].$message }}
                </small>
            </div>

            <!-- Gombok -->
            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" severity="secondary" @click="closeModal" />
                <Button label="Update" icon="pi pi-check" @click="updateEmployee" />
            </div>

        </div>
    </Dialog>
</template>
