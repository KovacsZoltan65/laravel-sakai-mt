<script setup>
import { onMounted, reactive, watch, ref, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/sakai/layout/AppLayout.vue";

const props = defineProps({
    title: String,
    filters: Object
});
</script>
<template>
    <Head :title="props.title"/>
    <AppLayout>

        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Hq Hierarchy</h1>
        </div>

    </AppLayout>
</template>
