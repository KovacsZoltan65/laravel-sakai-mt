<script setup>
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

</script>
<template>
    <div class="pt-2 pb-3 space-y-1">
        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
            Dashboard
        </ResponsiveNavLink>
    </div>
</template>