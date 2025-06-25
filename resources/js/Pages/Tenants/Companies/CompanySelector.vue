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
    <div class="card p-4 max-w-md mx-auto">
        <h2 class="text-lg font-semibold mb-2">Válassz egy céget</h2>

        <Select
            v-model="selectedCompany"
            :options="props.companies"
            optionLabel="name"
            optionValue="id"
            placeholder="Válassz céget"
            class="w-full mb-4"
        />

        <Button class="btn btn-primary w-full" @click="submit" :disabled="!selectedCompany">
            Tovább
        </Button>
    </div>
</template>
