<script setup>
import { ref } from 'vue';
import MenuService from "@/services/Menu/MenuService.js";
import EditModal from "@/Pages/Hq/MenuManager/Edit.vue";

const props = defineProps({
    item: Object,
    depth: Number,
    menuTree: Array
});

const emit = defineEmits(['refresh']);

const showEdit = ref(false);
const showAdd = ref(false);

// Törlés
const deleteItem = async () => {
    if (confirm(`Biztosan törlöd: ${props.item.label}?`)) {
        await MenuService.delete(props.item.id);
        emit('refresh');
    }
};

// Felfelé mozgatás
const moveUp = async () => {
    await MenuService.update(props.item.id, {
        ...props.item,
        order_index: props.item.order_index - 1,
    });
    emit('refresh');
};

// Lefelé mozgatás
const moveDown = async () => {
    await MenuService.update(props.item.id, {
        ...props.item,
        order_index: props.item.order_index + 1,
    });
    emit('refresh');
};

</script>

<template>
    <div
        :style="{ marginLeft: depth * 20 + 'px' }"
        class="mb-1 p-1 border rounded bg-gray-50">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i
                    v-if="item.icon"
                    :class="['pi', item.icon]"
                    class="text-sm"></i>
                <strong>{{ item.label }}</strong>
                <span v-if="item.route_name" class="ml-2 text-sm text-blue-600">[{{ item.route_name }}]</span>
            </div>

            <div class="flex gap-1 text-sm">
                <!-- FEL -->
                <Button icon="pi pi-arrow-up" severity="secondary" size="small" @click="moveUp" aria-label="Fel" />
                <!-- LE -->
                <Button icon="pi pi-arrow-down" severity="secondary" size="small" @click="moveDown" aria-label="Le" />
                <!-- SZERKESZTÉS -->
                <Button icon="pi pi-pencil" severity="secondary" size="small" @click="showEdit = true" aria-label="Szerkesztés" />
                <!-- HOZZÁADÁS -->
                <Button icon="pi pi-plus" severity="secondary" size="small" @click="showAdd = true" aria-label="Hozzáadás" />
                <!-- TÖRLÉS -->
                <Button icon="pi pi-trash" severity="secondary" size="small" @click="deleteItem" aria-label="Törlés" />
            </div>
        </div>

        <!-- Gyermekek rekurzívan -->
        <MenuItemNode
            v-for="child in item.items"
            :key="child.id"
            :item="child"
            :depth="depth + 1"
            :menuTree="menuTree"
            @refresh="emit('refresh')"
        />

        <!-- Szerkesztés -->
        <EditModal
            v-if="showEdit"
            :menuItem="item"
            :menuTree="menuTree"
            :parentId="item.parent_id"
            @close="showEdit = false"
            @saved="emit('refresh')"
        />

        <!-- Új hozzáadása -->
        <EditModal
            v-if="showAdd"
            :parentId="item.parent_id"
            :menuTree="menuTree"
            @close="showAdd = false"
            @saved="emit('refresh')"
        />
    </div>
</template>
