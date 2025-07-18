<script setup>
import { ref, watch, computed, onMounted } from 'vue';
//import { Dialog } from 'primevue/dialog';
//import { InputText } from 'primevue/inputtext';
//import { Button } from 'primevue/button';

import MenuService from "@/services/MenuService.js";

const props = defineProps({
  menuItem: Object,      // ha van: szerkesztés
  parentId: Number,      // ha új gyerek menüpont
});

const emit = defineEmits(['close', 'saved']);

const visible = ref(true);
const isSaving = ref(false);

const form = ref({
  label: '',
  route_name: '',
  icon: '',
  url: '',
  can: '',
  order_index: 0,
  parent_id: props.parentId ?? null
});

onMounted(() => {
  if (props.menuItem) {
    form.value = {
      label: props.menuItem.label ?? '',
      route_name: props.menuItem.route_name ?? '',
      icon: props.menuItem.icon ?? '',
      url: props.menuItem.url ?? '',
      can: props.menuItem.can ?? '',
      order_index: props.menuItem.order_index ?? 0,
      parent_id: props.menuItem.parent_id ?? null
    };
  }
});

const save = async () => {
  isSaving.value = true;

  try {
    if (props.menuItem?.id) {
      await MenuService.update(props.menuItem.id, form.value);
    } else {
      await MenuService.store(form.value);
    }

    emit('saved');
    visible.value = false;
  } catch (e) {
    console.error(e);
  } finally {
    isSaving.value = false;
  }
};
</script>

<template>
  <Dialog v-model:visible="visible" modal :closable="true" header="Menüpont szerkesztés" @hide="emit('close')" style="width: 30rem;">
    <div class="flex flex-col gap-4">

      <div>
        <label class="block mb-1 font-medium">Felirat</label>
        <InputText v-model="form.label" class="w-full" />
      </div>

      <div>
        <label class="block mb-1 font-medium">Ikon (pl: pi-cog)</label>
        <InputText v-model="form.icon" class="w-full" />
      </div>

      <div>
        <label class="block mb-1 font-medium">Laravel route neve</label>
        <InputText v-model="form.route_name" class="w-full" />
      </div>

      <div>
        <label class="block mb-1 font-medium">URL (ha nincs route)</label>
        <InputText v-model="form.url" class="w-full" />
      </div>

      <div>
        <label class="block mb-1 font-medium">Jogosultság (pl: menu.manage)</label>
        <InputText v-model="form.can" class="w-full" />
      </div>

      <div>
        <label class="block mb-1 font-medium">Sorrend (order_index)</label>
        <InputText v-model="form.order_index" type="number" class="w-full" />
      </div>

    </div>

    <template #footer>
      <Button label="Mégse" icon="pi pi-times" severity="secondary" @click="visible = false" />
      <Button label="Mentés" icon="pi pi-check" :loading="isSaving" @click="save" />
    </template>
  </Dialog>
</template>
