<script setup>
import { Head, router } from '@inertiajs/vue3'

const goToDashboard = () => {
    router.visit(route('dashboard'));
}

</script>

<template>
    <Head title="Maintenance" />

    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto mb-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9 9-4.03 9-9z" />
                        </svg>
                        <h1 class="text-3xl font-semibold text-surface-900 dark:text-surface-0 mb-4">Karbantartás folyamatban</h1>
                        <p class="text-muted-color text-lg">
                            Az alkalmazás jelenleg nem elérhető karbantartás miatt.<br>
                            Kérjük, próbáld meg később újra.
                        </p>

                        <Button
                            label="Vissza a főoldalra"
                            class="mt-8"
                            @click="goToDashboard"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.text-muted-color {
    color: var(--text-color-secondary);
}
</style>
