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
            <h4>TENANT DASHBOARD</h4>
        </div>
    </AppLayout>

</template>

<style scoped lang="scss"></style>