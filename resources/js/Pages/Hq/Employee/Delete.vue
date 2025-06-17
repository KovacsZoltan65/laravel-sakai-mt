<script setup>
import EmployeeService from '@/services/Employee/HqEmployeeService.js';

const props = defineProps({
    title: String,
    show: Boolean,
    employee: Object,
    tenantId: [Number, String],
});

const emit = defineEmits(['close', 'deleted']);

const deleteEmployee = async () => {
    try {
        await EmployeeService.hq_deleteEmployee(
            props.employee.id, {
                tenant_id: props.tenantId
            }
        );
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
        header="Cég törlése"
        @hide="closeModal"
        :style="{ width: '30vw' }"
    >
        <div class="text-center my-5 text-lg">
            Biztosan törölni szeretnéd a(z) <strong>{{ employee.name }}</strong> dolgozót?
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
