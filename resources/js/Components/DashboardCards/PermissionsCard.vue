<script setup>
const props = defineProps({
    permissions: Number,
});
</script>

<template>
    <div class="card mb-0">
        <div class="flex justify-between mb-4">
            <div>
                <span
                    class="block text-muted-color font-medium mb-4"
                >Permission</span>
                <div
                    class="text-surface-900 dark:text-surface-0 font-medium text-xl"
                >
                    {{ props.permissions }}
                </div>
            </div>
            <div
                class="flex items-center justify-center bg-cyan-100 dark:bg-cyan-400/10 rounded-border"
                style="width: 2.5rem; height: 2.5rem"
            >
                <i class="pi pi-key text-cyan-500 !text-xl"></i>
            </div>
        </div>
    </div>
</template>