<script setup>
import { onMounted, reactive, watch } from "vue";
import AppLayout from "@/sakai/layout/AppLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Inertia } from '@inertiajs/inertia';

import AppSettingService from "@/services/Settings/AppSettingsService.js";

import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";
import DeleteModal from "./Delete.vue";

import { usePermissions } from "@/composables/usePermissions";
import { useDataTableFetcher } from "@/composables/useDataTableFetcher";
const { has } = usePermissions();

const props = defineProps({
    title: String,
    filters: Object
});
</script>
<template></template>
