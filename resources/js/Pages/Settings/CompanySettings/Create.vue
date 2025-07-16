<script setup>
import { ref, computed, watch } from "vue";
import useVuelidate from "@vuelidate/core";
import { required, minLength } from "@vuelidate/validators";
import { getTypes } from '@/helpers/functions.js';
import CompanySettingsService from "@/services/Settings/CompanySettingsService";
import { useToast } from "primevue/usetoast";
import TimezoneSelect from "@/Components/TimezoneSelect.vue";
import LanguageSelect from "@/Components/LanguageSelect.vue";
import ThemeSelect from "@/Components/ThemeSelect.vue";
const toast = useToast();

const props = defineProps({
    show: Boolean,
    title: String
});

const emit = defineEmits(['close', 'saved']);

const isSaving = ref(false);

const form = ref({
    key: '',
    type: 'string',
    value: '',
});

const rules = computed(() => ({
    key: { required, minLength: minLength(3) },
    type: { required },
    value: { required }
}));

const v$ = useVuelidate(rules, form);

// 🔄 Reset when opened
watch(() => props.show, (showing) => {
    if (showing) {
        form.value = {
            key: '',
            type: 'string',
            value: ''
        };
        v$.value.$reset();
    }
});

// 🧠 Submit
const createSetting = async () => {
    v$.value.$touch();
    if (v$.value.$invalid) return;

    isSaving.value = true;
    try {
        await CompanySettingsService.createSetting({
            key: form.value.key,
            type: form.value.type,
            value: form.value.value
        });

        toast.add({
            severity: 'success',
            summary: 'Sikeres létrehozás',
            life: 3000
        });

        emit('saved');
        closeModal();
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Hiba',
            detail: e.message ?? 'Létrehozás sikertelen',
            life: 5000
        });
    } finally {
        isSaving.value = false;
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
        header="Új beállítás"
        @hide="closeModal"
        :style="{ width: '550px' }"
    >
        <div class="flex flex-col gap-4 mt-4">

            <!-- KEY -->
            <div>
                <label class="font-bold" for="key">Kulcs:</label>
                <InputText
                    id="key"
                    v-model="form.key"
                    class="w-full"
                    :class="{ 'p-invalid': v$.key.$error }"
                />
                <small v-if="v$.key.$error" class="text-red-500">
                    {{ v$.key.$errors[0].$message }}
                </small>
            </div>

            <!-- TYPE -->
            <div>
                <label class="font-bold" for="type">Típus:</label>
                <Select
                    id="type" class="w-full"
                    v-model="form.type"
                    :options="getTypes()"
                    optionLabel="label"
                    optionValue="value"
                />
            </div>

            <!-- VALUE -->
            <div>
                <label class="font-bold" for="value">Érték:</label>

                <!-- IDŐZÓNA -->
                <template v-if="form.type === 'timezone' || form.key === 'timezone'">
                    <TimezoneSelect v-model="form.value" />
                </template>

                <!-- NYELV -->
                <template v-else-if="form.type === 'locale' || form.key === 'locale'">
                    <LanguageSelect v-model="form.value" />
                </template>

                <!-- THEME -->
                <template v-else-if="form.type === 'theme' || form.key === 'theme'">
                    <ThemeSelect v-model="form.value" />
                </template>

                <!-- LOGIKAI -->
                <template v-if="form.type === 'bool' || form.key === 'bool'">
                    <div class="mt-2">
                        <ToggleSwitch
                            id="value" name="value"
                            v-model="form.value"
                        />
                    </div>
                </template>

                <!-- EGÉSZ SZÁM -->
                <template v-else-if="form.type === 'int' || form.key === 'int'">
                    <InputNumber
                        v-model="form.value"
                        class="w-full"
                        showButtons fluid
                        :min="1" :max="7"
                        buttonLayout="horizontal" />
                </template>

                <!-- JSON -->
                <template v-else-if="form.type === 'json' || form.key === 'json'">
                    <Textarea v-model="form.value" class="w-full" rows="6" />
                </template>

                <!-- SZÖVEG -->
                <template v-else-if="form.type === 'string' || form.key === 'string'">
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
                    label="Létrehozás"
                    icon="pi pi-check"
                    @click="createSetting"
                    :loading="isSaving"
                />
            </div>
        </div>
    </Dialog>
</template>
