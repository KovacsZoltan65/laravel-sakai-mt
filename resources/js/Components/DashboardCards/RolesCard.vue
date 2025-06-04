<script setup>
const props = defineProps({
    roles: Number,
});
</script>

<template>
    <div class="card mb-0">
        <div class="flex justify-between mb-4">
            <div>
                <span
                    class="block text-muted-color font-medium mb-4"
                >Role</span>
                <div
                    class="text-surface-900 dark:text-surface-0 font-medium text-xl"
                >
                    {{ props.roles }}
                </div>
            </div>
            <div
                class="flex items-center justify-center bg-orange-100 dark:bg-orange-400/10 rounded-border"
                style="width: 2.5rem; height: 2.5rem"
            >
                <i
                    class="pi pi-unlock text-orange-500 !text-xl"
                ></i>
            </div>
        </div>
    </div>
</template>