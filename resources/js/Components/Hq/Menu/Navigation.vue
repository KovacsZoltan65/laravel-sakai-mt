<script setup>
import NavLink from '@/Components/NavLink.vue';

</script>

<template>
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
            Hq Dashboard
        </NavLink>
    </div>
</template>