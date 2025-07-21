<script setup>
import iconOptions from "@/helpers/icons.js";
const model = defineModel(); // v-model támogatás
</script>

<template>
    <Select
        v-model="model"
        :options="iconOptions"
        optionLabel="label"
        optionValue="value"
        placeholder="Ikon kiválasztása"
        class="w-full"
        filter
    >
    <template #option="slotProps">
      <div class="flex items-center gap-2">
        <i :class="slotProps.option.value" />
        <span>{{ slotProps.option.label }}</span>
      </div>
    </template>
    <template #value="slotProps">
      <div v-if="slotProps.value" class="flex items-center gap-2">
        <i :class="slotProps.value" />
        <span>
          {{
            iconOptions.find((opt) => opt.value === slotProps.value)?.label ??
            slotProps.value
          }}
        </span>
      </div>
      <span v-else class="text-gray-400">Ikon kiválasztása</span>
    </template>
  </Select>
</template>
