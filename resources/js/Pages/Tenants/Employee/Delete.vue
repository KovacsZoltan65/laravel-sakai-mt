<script setup>
import EmployeeService from "@/services/Employee/EmployeeService.js";

import { useToast } from "primevue/usetoast";
const toast = useToast();

const props = defineProps({
    employee: Object,
    title: String,
    show: Boolean
});

const emit = defineEmits(['close', 'deleted']);

const deleteEmployee = async () => {
    try {
        await EmployeeService.deleteEmployee(props.employee.id);

        toast.add({
            severity: 'success',
            summary: 'Sikeres törlés',
            life: 3000
        });

        emit('deleted', props.employee.id);
        closeModal();
    } catch (e) {
        console.error("Törlés sikertelen", e);

        toast.add({
            severity: 'error',
            summary: 'Hiba',
            detail: e.message ?? 'Mentés sikertelen',
            life: 5000
        });
    } finally {
        //
    }
};

// Modal bezárása
const closeModal = () => {
    emit('close');
};

</script>

<template>
    <Dialog
        :visible="show" modal
        header="Dolgozó törlése"
        @hide="closeModal"
        :style="{ width: '30vw' }"
    >
        <div class="text-center my-5 text-lg">
            Biztosan törölni szeretnéd a(z) <strong>
                {{ employee.name }}
            </strong> dolgozót?
        </div>

        <template #footer>
            <Button
                label="Mégse"
                icon="pi pi-times"
                @click="closeModal"
                severity="secondary"
                class="p-button-text"
            />
            <Button
                label="Törlés"
                icon="pi pi-trash"
                @click="deleteEmployee"
                severity="danger"
            />
        </template>
    </Dialog>
</template>
