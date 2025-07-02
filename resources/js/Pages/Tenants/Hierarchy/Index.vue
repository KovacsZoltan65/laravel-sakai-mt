<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/sakai/layout/AppLayout.vue'
import HierarchyCanvas from '@/Components/Hierarchy/HierarchyCanvas.vue'

const props = defineProps({
    title: String,
    filters: Object
})
</script>

<template>

    <Head :title="props.title" />

    <AppLayout>
        <div class="h-full overflow-hidden">
            <HierarchyCanvas />
        </div>
    </AppLayout>
    
</template>
