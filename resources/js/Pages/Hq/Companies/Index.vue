<script setup>
    import { onMounted, reactive, watch } from "vue";
    import AppLayout from "@/sakai/layout/AppLayout.vue";
    import { Head } from "@inertiajs/vue3";
    
    import CompanyService from "@/services/Company/CompanyService.js";
    
    import { usePermissions } from "@/composables/usePermissions";
    import { useDataTableFetcher } from "@/composables/useDataTableFetcher";
    const { has } = usePermissions();
    
    const props = defineProps({
        title: String,
        filters: Object
    });
    
</script>

<template>
    
    <Head :title="props.title"/>
    
    <AppLayout></AppLayout>
</template>
