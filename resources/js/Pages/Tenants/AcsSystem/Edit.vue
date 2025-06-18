<script setup>
import { ref, computed, watch } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";
import AcsService from "@/services/AcsSystem/AcsSystemService.js";

import { useToast } from "primevue/usetoast";
const toast = useToast();

const props = defineProps({
    show: Boolean,
    title: String,
    employee: Object,
});

const emit = defineEmits(['close', 'saved']);

const isSaving = ref(false);

// Űrlap adatok
const form = ref({
    id: null,
    name: ''
});

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
}));

const v$ = useVuelidate(rules, form);

watch(
    () => props.employee,
    (newEmployee) => {
        if (newEmployee) {
            form.value = {
                id: newEmployee.id,
                name: newEmployee.name || '',
            };
            v$.value.$reset(); // Reseteljük a validációt, hogy ne legyenek előző hibák
        }
    },
    { immediate: true }
);

// Frissítés (update) művelet
const updateAcs = async () => {
    isSaving.value = true;

    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            // A szerkesztett entitás azonosítóját props.employee.id használjuk
            await AcsService.updateAcsService(props.acs_service.id, form.value);

            toast.add({
                severity: 'success',
                summary: 'Sikeres frissítés',
                life: 3000
            });

            emit('saved', form.value);
            closeModal();
        } catch(e) {
            console.error('Frissítés sikertelen', e);

            toast.add({
                severity: 'error',
                summary: 'Hiba',
                detail: e.message ?? 'Frissítés sikertelen',
                life: 5000
            });
        } finally {
            isSaving.value = false;
        }
    }
};

const closeModal = () => {
    v$.value.$reset();
    emit('close');
};

</script>

<template>
    <Dialog
        :visible="show" modal
        header="Edit Acs System"
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

            <!-- Gombok -->
            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" severity="secondary" @click="closeModal" />
                <Button label="Update" icon="pi pi-check" @click="updateAcs" />
            </div>

        </div>
    </Dialog>
</template>
