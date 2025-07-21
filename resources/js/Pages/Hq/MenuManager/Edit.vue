<script setup>
import { ref, watch, computed, onMounted } from "vue";
import { usePage } from '@inertiajs/vue3';
import MenuService from "@/services/Menu/MenuService.js";

// =======================================
// SELECTORS
// =======================================
import {IconSelector, PermissionSelector, RouteSelector} from "@/Components/Selectors";

const props = defineProps({
  menuItem: Object, // ha van: szerkesztés
  parentId: Number, // ha új gyerek menüpont
  menuTree: Array,
});

const emit = defineEmits(["close", "saved"]);
const page = usePage();
const visible = ref(true);
const isSaving = ref(false);

const form = ref({
  label: "",
  route_name: "",
  icon: "",
  url: "",
  can: "",
  order_index: 0,
  parent_id: props.parentId ?? null,
});

// 🔄 Meghatározza az adott parent_id alatt a legnagyobb order_index + 1 értéket
function getNextOrderIndex(tree, parentId) {
    const matches = [];

    const traverse = (nodes) => {
        if (!Array.isArray(nodes)) return;

        nodes.forEach((node) => {
            if (node.parent_id === parentId) {
                matches.push(node.order_index);
            }
            if (node.items?.length) {
                traverse(node.items);
            }
        });
    };

    traverse(tree);

    if (matches.length === 0) return 0;
    return Math.max(...matches) + 1;
}

onMounted(() => {
    if (props.menuItem) {
        form.value = {
            label: props.menuItem.label ?? '',
            route_name: props.menuItem.route_name ?? '',
            icon: props.menuItem.icon ?? '',
            url: props.menuItem.url ?? '',
            can: props.menuItem.can ?? '',
            order_index: props.menuItem.order_index ?? 0,
            parent_id: props.menuItem.parent_id ?? null,
        };
    } else {
        form.value.order_index = getNextOrderIndex(props.menuTree ?? [], props.parentId ?? null);
    }
});

// Mentés
const save = async () => {
    isSaving.value = true;

console.log('props.menuItem', props.menuItem);

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
