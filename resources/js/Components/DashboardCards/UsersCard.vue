<script setup>
const props = defineProps({
    users: Number,
});
</script>

<template>
    <div class="card mb-0">
        <div class="flex justify-between mb-4">
            <div>
                <span
                    class="block text-muted-color font-medium mb-4"
                >User</span>
                <div
                    class="text-surface-900 dark:text-surface-0 font-medium text-xl"
                >
                    {{ props.users }}
                </div>
            </div>
            <div
                class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded-border"
                style="width: 2.5rem; height: 2.5rem"
            >
                <i class="pi pi-users text-blue-500 !text-xl"></i>
            </div>
        </div>
    </div>
</template>