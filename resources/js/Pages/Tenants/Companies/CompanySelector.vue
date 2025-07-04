<script setup>
    import { ref } from 'vue';
    import { router } from '@inertiajs/vue3';

    const props = defineProps({
    companies: Array
    });

    const selectedCompany = ref(null);

    const submit = () => {
    if (selectedCompany.value) {
        router.post('/select-company', {
        company_id: selectedCompany.value
        });
    }
    };
</script>

<template>
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div
                style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)"
            >
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <svg viewBox="0 0 54 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-8 w-16 shrink-0 mx-auto">
                            <!-- Logo (meghagyhatod vagy eltávolíthatod) -->
                            <path d="..." fill="var(--primary-color)" />
                        </svg>
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Cég kiválasztása</div>
                        <span class="text-muted-color font-medium">Válassz a rendelkezésre álló cégek közül</span>
                    </div>

                    <form @submit.prevent="submit">
                        <div>
                            <label for="company" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Cég</label>
                            <Select
                                id="company"
                                v-model="selectedCompany"
                                :options="props.companies"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Válassz céget"
                                class="w-full md:w-[30rem] mb-6"
                            />

                            <Button type="submit" label="Tovább" class="w-full" :disabled="!selectedCompany" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
