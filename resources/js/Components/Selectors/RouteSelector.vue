<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const model = defineModel();
const page = usePage();

const routeOptions = computed(() => {
    return Object.keys(page.props.ziggy?.routes || {})
        .filter(
            (name) =>
                !name.startsWith("debugbar.") &&
                !name.startsWith("ignition.") &&
                !name.startsWith("sanctum.") &&
                !name.includes("csrf") &&
                !name.includes("assets") &&
                !name.includes("openhandler")
        )
        .sort();
});
</script>

<template>
    <Select
        v-model="model"
        :options="routeOptions"
        placeholder="Laravel route kiválasztása"
        class="w-full"
        filter
    />
</template>
