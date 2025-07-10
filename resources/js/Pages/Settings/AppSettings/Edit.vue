<script setup>
import { ref, watch } from "vue";
import { getTypes } from '@/helpers/functions.js';
import AppSettingService from "@/services/Settings/AppSettingsService.js";
import TimezoneSelect from "@/Components/TimezoneSelect.vue";
import { useToast } from "primevue/usetoast";
const toast = useToast();

const props = defineProps({
    show: Boolean,
    title: String,
    setting: Object,
});

const emit = defineEmits(['close', 'saved']);
const isSaving = ref(false);

// ⏬ Form adatok
const form = ref({
    id: null,
    key: '',
    type: 'string',
    value: '',
});

// ⏫ Ha új setting jön, töltsük be
watch(() => props.setting, (newSetting) => {
    if (newSetting) {
        form.value = {
            id: newSetting.id,
            key: newSetting.key,
            type: newSetting.type,
            value: parseFormValue(newSetting.value, newSetting.type)
        };
    }
}, { immediate: true });

// 📦 JSON/string átalakító
function parseFormValue(value, type) {
    if (type === 'bool') return value == '1' || value === true;
    if (type === 'int') return parseInt(value);
    if (type === 'json') {
        try {
            return JSON.stringify(JSON.parse(value), null, 2);
        } catch { return value }
    }
    return value;
}

// ⬆️ Update művelet
const updateSetting = async () => {
    isSaving.value = true;
    try {
        await AppSettingService.updateSetting(form.value.id, {
            value: form.value.value
        });

        toast.add({
            severity: 'success',
            summary: 'Beállítás frissítve',
            life: 3000
        });

        emit('saved', form.value);
        closeModal();
    } catch (e) {
        console.error('Hiba mentéskor', e);
        toast.add({
            severity: 'error',
            summary: 'Mentés sikertelen',
            detail: e.message ?? 'Valami hiba történt.',
            life: 5000
        });
    } finally {
        isSaving.value = false;
    }
};

// ❌ Bezárás
const closeModal = () => {
    emit('close');
};

</script>

<template>
    <Dialog
        :visible="show" modal
        header="Beállítás szerkesztése"
        @hide="closeModal"
        :style="{ width: '550px' }"
    >
        <div class="flex flex-col gap-4 mt-4">

            <!-- KEY mező (letiltva) -->
            <div>
                <label class="font-bold" for="key">Kulcs:</label>
                <InputText
                    v-model="form.key" disabled
                    class="w-full" id="key"
                />
            </div>

            <!-- TYPE mező (letiltva) -->
            <div>
                <label class="font-bold" for="type">Típus:</label>
                <!--<InputText v-model="form.type" disabled class="w-full" id="type" />-->
                <Select
                    id="type" class="w-full"
                    v-model="form.type"
                    :options="getTypes()"
                    optionLabel="label"
                    optionValue="value"
                    disabled
                />
            </div>

            <!-- VALUE mező dinamikusan -->
            <div>
                <label class="font-bold">Érték:</label>

                <!-- IDŐZÓNA -->
                <template v-if="form.type === 'timezone' || form.key === 'timezone'">
                    <TimezoneSelect v-model="form.value" />
                </template>

                <!-- LOGIKAI -->
                <template v-else-if="form.type === 'bool'">
                    <ToggleSwitch v-model="form.value" />
                </template>

                <!-- EGÉSZ SZÁM -->
                <template v-else-if="form.type === 'int'">
                    <InputNumber
                        v-model="form.value"
                        class="w-full"
                        showButtons fluid
                        :min="1" :max="7"
                        buttonLayout="horizontal" />
                </template>

                <!-- JSON -->
                <template v-else-if="form.type === 'json'">
                    <Textarea v-model="form.value" class="w-full" rows="6" />
                </template>

                <!-- SZÖVEG -->
                <template v-else>
                    <InputText v-model="form.value" class="w-full" />
                </template>
            </div>

            <!-- Gombok -->
            <div class="flex justify-end gap-2 mt-4">
                <Button
                    label="Mégsem"
                    severity="secondary"
                    @click="closeModal"
                />
                <Button
                    label="Mentés"
                    icon="pi pi-check"
                    @click="updateSetting"
                    :loading="isSaving"
                />
            </div>
        </div>
    </Dialog>
</template>
