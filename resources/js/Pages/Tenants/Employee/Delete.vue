<script setup>
import EmployeeService from "@/services/Employee/EmployeeService.js";

const props = defineProps({
    employee: Object,
    title: String,
    show: Boolean
});

const emit = defineEmits(['close', 'deleted']);

const deleteEmployee = async () => {
    try {
        await EmployeeService.deleteEmployee(props.employee.id);
        emit('deleted', props.employee.id);
        closeModal();
    } catch (e) {
        console.error("Törlés sikertelen", e);
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
