<script setup>
import { onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useRoute } from 'vue-router';
import axios from 'axios';

const key = useRoute().params.key;
const result = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const res = await axios.get(`/api/settings/resolve/${key}`);
        result.value = res.data;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Beállítás részletei: <span class="text-primary">{{ key }}</span></h2>

    <div v-if="loading">Betöltés...</div>
    <div v-else-if="!result">Nem található adat.</div>
    <div v-else class="space-y-4">

      <div>
        <strong>Érték:</strong>
        <span class="font-mono">{{ result.value ?? '—' }}</span>
      </div>

      <div>
        <strong>Forrás:</strong>
        <span class="text-sm italic">{{ result.source ?? 'nincs' }}</span>
      </div>

      <div>
        <strong>Aktív:</strong>
        <span :class="result.active ? 'text-green-600' : 'text-red-600'">
          {{ result.active ? 'Igen' : 'Nem' }}
        </span>
      </div>

      <div v-if="result.active_when">
        <strong>Aktiválási feltételek:</strong>
        <pre class="bg-gray-100 p-2 rounded text-sm">{{ result.active_when }}</pre>
      </div>

      <div v-if="result.dependencies.length">
        <strong>Függőségek:</strong>
        <ul class="list-disc list-inside text-sm">
          <li v-for="dep in result.dependencies" :key="dep">
            {{ dep }} = <span class="font-mono">{{ result.resolved_dependencies[dep] ?? 'nincs érték' }}</span>
          </li>
        </ul>
      </div>

      <div v-if="result.meta">
        <strong>Leírás:</strong>
        <div class="text-sm text-gray-700">{{ result.meta.description }}</div>
        <div class="text-sm mt-2">
          <span class="text-gray-600">Típus:</span> {{ result.meta.type }},
          <span class="text-gray-600">Scope:</span> {{ result.meta.scope }},
          <span class="text-gray-600">Alapérték:</span> <code>{{ result.meta.default_value }}</code>
        </div>
      </div>

    </div>
  </div>
</template>
