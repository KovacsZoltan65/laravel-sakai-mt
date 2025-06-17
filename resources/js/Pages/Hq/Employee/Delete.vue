<script setup>
import EmployeeService from '@/services/Employee/EmployeeService';

const props = defineProps({
    entity: Object,
    title: String,
    show: Boolean
});

const emit = defineEmits(['close', 'deleted']);

const deleteEntity = async () => {
    try {
        await EntityService.deleteEntity(props.entity.id);
        emit('deleted', props.entity.id);
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

<template></template>
