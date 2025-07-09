<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/sakai/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

import AppSettingService from '@/services/Settings/AppSettingsService';
import CompanySettingService from '@/services/Settings/CompanySettingsService';
import UserSettingService from '@/services/Settings/UserSettingsService';

const props = defineProps({ title: String });

const appSettings = ref([]);
const companySettings = ref([]);
const userSettings = ref([]);
const isLoading = ref(false);

const loadSettings = async () => {
    isLoading.value = true;
    try {
        const [appRes, companyRes, userRes] = await Promise.all([
            AppSettingService.getSettings(),
            CompanySettingService.getSettings(),
            UserSettingService.getSettings()
        ]);
        appSettings.value = appRes.data?.data ?? [];
        companySettings.value = companyRes.data?.data ?? [];
        userSettings.value = userRes.data?.data ?? [];
    } catch (e) {
        console.error('Hiba a beállítások betöltésekor:', e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(loadSettings);
</script>

<template>

    <Head :title="props.title" />
    <AppLayout>
        <div class="card">
            <h1 class="text-2xl font-bold mb-4">Összesített beállítások (User → Company → App)</h1>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- USER -->
                <div>
                    <h2 class="text-lg font-semibold mb-2">User Settings</h2>
                    <ul class="border rounded p-2 bg-gray-50">
                        <li v-for="item in userSettings" :key="item.key" class="py-1 border-b text-sm">
                            <span class="font-medium">{{ item.key }}:</span> {{ item.value }}
                        </li>
                    </ul>
                </div>

                <!-- COMPANY -->
                <div>
                    <h2 class="text-lg font-semibold mb-2">Company Settings</h2>
                    <ul class="border rounded p-2 bg-gray-50">
                        <li v-for="item in companySettings" :key="item.key" class="py-1 border-b text-sm">
                            <span class="font-medium">{{ item.key }}:</span> {{ item.value }}
                        </li>
                    </ul>
                </div>

                <!-- APP -->
                <div>
                    <h2 class="text-lg font-semibold mb-2">App Settings</h2>
                    <ul class="border rounded p-2 bg-gray-50">
                        <li v-for="item in appSettings" :key="item.key" class="py-1 border-b text-sm">
                            <span class="font-medium">{{ item.key }}:</span> {{ item.value }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
