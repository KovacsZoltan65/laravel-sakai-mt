<script setup>
import { ref, computed } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength, maxLength, email } from "@vuelidate/validators";

const props = defineProps({
    show: Boolean,
    title: String,
});

const emit = defineEmits(["close", "saved"]);

// Form adatok
const form = ref({
    name: '',
    email: '',
    // ide jön minden egyéb mező
});

// Validációs szabályok
const rules = computed(() => ({
    name: { required, minLength: minLength(3), maxLength: maxLength(255) },
    email: { required, email }
}));
const v$ = useVuelidate(rules, form);

// Mentés
const save = async () => {
    v$.value.$touch();
    if (!v$.value.$invalid) {
        try {
            // Itt mehet az axios.post...
            await axios.post('/api/companies', form.value)

            emit('saved', form.value);
            closeModal();
        } catch (e) {
            console.error('Mentés sikertelen', e);
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
        header="Create company"
        @hide="closeModal"
    >

        <div class="flex flex-col gap-6" style="margin-top: 17px;"></div>

        <div class="flex justify-end gap-2 mt-4">
            <Button label="Cancel" severity="secondary" @click="closeModal" />
            <Button label="Save" icon="pi pi-check" @click="save" />
        </div>

    </Dialog>
</template>