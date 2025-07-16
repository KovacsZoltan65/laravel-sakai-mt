<script setup>
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import AppSettingsService from '@/services/Settings/AppSettingsService';

const toast = useToast();
const props = defineProps({ show: Boolean, setting: Object });
const emit = defineEmits(['close', 'deleted']);

const isDeleting = ref(false);

const confirmDelete = async () => {
    isDeleting.value = true;
    try {
        await AppSettingsService.deleteSetting(props.setting.id);
        toast.add({ severity: 'success', summary: 'Törölve', life: 3000 });
        emit('deleted');
        close();
    } catch (err) {
        toast.add({ severity: 'error', summary: 'Hiba', detail: err.message || 'Törlés sikertelen', life: 5000 });
    } finally {
        isDeleting.value = false;
    }
};

const close = () => emit('close');
</script>
<template>
    <Dialog :visible="show" modal header="Törlés megerősítése" @hide="close" :style="{ width: '400px' }">

        <!-- FELIRAT -->
        <div
            class="mb-4"
        >Biztosan törlöd a(z) <strong>{{ props.setting?.key }}</strong> beállítást?</div>

        <div class="flex justify-end gap-2">

            <!-- MÉGSEM GOMB -->
            <Button
                label="Mégsem"
                severity="secondary"
                @click="close"
            />

            <!-- TÖRLÉS GOMB -->
            <Button
                label="Törlés"
                icon="pi pi-trash"
                severity="danger"
                :loading="isDeleting"
                @click="confirmDelete"
            />
        </div>
    </Dialog>
</template>
