<script setup>
defineProps({
    value: {
        type: [String, Number, Boolean],
        required: true
    },
    type: {
        type: String,
        required: true
    }
});
</script>

<template>
    <span v-if="type === 'bool'">
        <i class="pi"
            :class="value == true || value == '1' ? 'pi-check-circle text-green-500' : 'pi-times-circle text-red-500'" />
    </span>

    <span v-else-if="type === 'int'" class="text-right font-mono text-blue-600">
        {{ value }}
    </span>

    <span v-else-if="type === 'json'" class="text-gray-500 font-mono text-xs line-clamp-1">
        {{
            (() => {
                try {
                    return JSON.stringify(JSON.parse(value), null, 0)
                } catch {
                    return value
                }
            })()
        }}
    </span>

    <span v-else>
        {{ value }}
    </span>
</template>
