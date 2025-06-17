<script setup>
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    title: String
});

</script>

<template>

    <Head :title="props.title"/>

    <AppLayout>
        <div class="grid grid-cols-12 gap-8">
            <h4>ACS SYSTEMS INDEX</h4>
        </div>
    </AppLayout>

</template>