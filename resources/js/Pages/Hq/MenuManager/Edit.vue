<script setup>
import { ref, watch, computed, onMounted } from "vue";
import { usePage } from '@inertiajs/vue3';
//import { Dialog } from 'primevue/dialog';
//import { InputText } from 'primevue/inputtext';
//import { Button } from 'primevue/button';

import MenuService from "@/services/MenuService.js";
//import { availableIcons } from '@/constants/icons.js';

//import { availableIcons } from "@/helpers/constants.js";
//import iconOptions from "@/helpers/icons.js";

// =======================================
// SELECTORS
// =======================================
import {IconSelector, PermissionSelector, RouteSelector} from "@/Components/Selectors";

// =======================================
// ICON SELECTOR
// =======================================
//import IconSelector from "@/Components/IconSelector.vue";

// =======================================
// PERMISSION SELECTOR
// =======================================
//import PermissionSelector from "@/Components/PermissionSelector.vue";

// =======================================
// ROUTE SELECTOR
// =======================================
//import RouteSelector from "@/Components/RouteSelector.vue";

/*
const iconOptions = [
    { label: 'Kezdőlap', value: 'pi pi-home' },
    { label: 'Beállítások', value: 'pi pi-cog' },
    { label: 'Felhasználók', value: 'pi pi-users' },
    { label: 'Hierarchia', value: 'pi pi-share-alt' },
    { label: 'Cégek', value: 'pi pi-building' },
    { label: 'Menü', value: 'pi pi-bars' },
];
*/
const props = defineProps({
  menuItem: Object, // ha van: szerkesztés
  parentId: Number, // ha új gyerek menüpont
});

const emit = defineEmits(["close", "saved"]);
const page = usePage();
const visible = ref(true);
const isSaving = ref(false);

// 📦 $page alapján jogosultság lista
//const permissionOptions = computed(() =>
//  page.props.auth?.user?.roles?.[0]?.permissions?.map(p => p.name) ?? []
//);

// 📦 $page alapján Ziggy route lista
//const routeNames = computed(() =>
//  Object.keys(page.props.ziggy.routes || {})
//    .filter(name => !name.startsWith('debugbar.') && !name.startsWith('ignition.'))
//    .sort()
//);
/*
const routeNames = computed(() =>
    Object.keys(page.props.ziggy.routes || {})
        .filter(name =>
            !name.startsWith('debugbar.') &&
            !name.startsWith('ignition.') &&
            !name.startsWith('sanctum.') &&
            !name.includes('csrf') // további tisztítás
        )
        .sort()
);
*/
const form = ref({
  label: "",
  route_name: "",
  icon: "",
  url: "",
  can: "",
  order_index: 0,
  parent_id: props.parentId ?? null,
});

onMounted(() => {
    if (props.menuItem) {
        form.value = {
            label: props.menuItem.label ?? "",
            route_name: props.menuItem.route_name ?? "",
            icon: props.menuItem.icon ?? "",
            url: props.menuItem.url ?? "",
            can: props.menuItem.can ?? "",
            order_index: props.menuItem.order_index ?? 0,
            parent_id: props.menuItem.parent_id ?? null,
        };
    }
});

// Mentés
const save = async () => {
    isSaving.value = true;

    try {
        if (props.menuItem?.id) {
            await MenuService.update(props.menuItem.id, form.value);
        } else {
            await MenuService.store(form.value);
        }

        emit("saved");
        visible.value = false;
    } catch (e) {
        console.error(e);
    } finally {
        isSaving.value = false;
    }
};
/*
const iconTemplate = (option) => {
    return option
        ? `<i class="${option.value} mr-2"></i> ${option.label}`
        : '';
};
*/
/*
// 🔍 Route kereső támogatás
// Autocomplete route-hoz
const filteredRoutes = ref([]);
const searchRoutes = (event) => {
    const keyword = event.query.toLowerCase();
    filteredRoutes.value = routeNames.value.filter(name =>
        name.toLowerCase().includes(keyword)
    );
    console.log(filteredRoutes.value);
};
*/
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :closable="true"
        header="Menüpont szerkesztés"
        @hide="emit('close')"
        style="width: 30rem"
    >
    <div class="flex flex-col gap-4">

        <!-- FELIRAT -->
        <div>
            <label class="block mb-1 font-medium">Felirat</label>
            <InputText v-model="form.label" class="w-full" />
        </div>

        <!-- ICON -->
        <div>
            <label class="block mb-1 font-medium">Ikon (pl: pi-cog)</label>
            <IconSelector v-model="form.icon" />
        </div>

        <!-- ROUTE -->
        <div>
            <label class="block mb-1 font-medium">Laravel route neve</label>
            <RouteSelector v-model="form.route_name" />
            <!--<Select
                v-model="form.route_name"
                :options="routeNames"
                placeholder="Laravel route kiválasztása"
                class="w-full"
                filter
            />-->
        </div>

        <!-- URL -->
        <div>
            <label class="block mb-1 font-medium">URL (ha nincs route)</label>
            <InputText v-model="form.url" class="w-full" />
        </div>

        <!-- JOGOSULTSÁG -->
        <div>
            <label class="block mb-1 font-medium">Jogosultság (pl: menu.manage)</label>
            <PermissionSelector v-model="form.can" />
        </div>

        <!-- SORREND -->
        <div>
            <label class="block mb-1 font-medium">Sorrend (order_index)</label>
            <InputNumber v-model="form.order_index" :min="0" class="w-full" />
        </div>
    </div>

    <template #footer>
        <Button label="Mégse" icon="pi pi-times" severity="secondary" @click="visible = false" />
        <Button label="Mentés" icon="pi pi-check" :loading="isSaving" @click="save" />
    </template>
  </Dialog>
</template>
