<script setup>
import NavLink from '@/Components/NavLink.vue';
</script>

<template>
    <div class="pt-2 pb-3 space-y-1">
        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
            Tenant Dashboard
        </NavLink>
    </div>
</template>