<script setup>
import { useLayout } from '@/sakai/layout/composables/layout';
import { onBeforeMount, ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import NavLink from "@/Components/NavLink.vue";

const { layoutState, setActiveMenuItem, onMenuToggle } = useLayout();

const page = usePage();

const props = defineProps({
    item: {
        type: Object,
        default: () => ({})
    },
    index: {
        type: Number,
        default: 0
    },
    root: {
        type: Boolean,
        default: true
    },
    parentItemKey: {
        type: String,
        default: null
    }
});

const isActiveMenu = ref(false);
const itemKey = ref(null);

onBeforeMount(() => {
    itemKey.value = props.parentItemKey ? props.parentItemKey + '-' + props.index : String(props.index);

    const activeItem = layoutState.activeMenuItem;

    isActiveMenu.value = activeItem === itemKey.value || activeItem ? activeItem.startsWith(itemKey.value + '-') : false;
});

watch(
    () => layoutState.activeMenuItem,
    (newVal) => {
        isActiveMenu.value = newVal === itemKey.value || newVal.startsWith(itemKey.value + '-');
    }
);

const itemClick = (event, item) => {
    if (item.disabled) {
        event.preventDefault();
        return;
    }

    if ((item.to || item.url) && (layoutState.staticMenuMobileActive || layoutState.overlayMenuActive)) {
        onMenuToggle();
    }

    if (item.command) {
        item.command({ originalEvent: event, item: item });
    }

    const foundItemKey = item.items ? (isActiveMenu.value ? props.parentItemKey : itemKey) : itemKey.value;

    setActiveMenuItem(foundItemKey);
};

const isActive = computed(() => {
    try {
        const url = new URL(props.item.to, window.location.origin);
        return page.url.startsWith(url.pathname);
    } catch {
        return false;
    }
});

</script>

<template>
    <li :class="{ 'layout-root-menuitem': root, 'active-menuitem': isActiveMenu }">
        <!-- Címke, ha nincs útvonal és nincs URL -->
        <div v-if="!item.to && !item.url && item.visible !== false" class="layout-menu-label">
            <i v-if="item.icon" :class="item.icon" class="layout-menuitem-icon"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
        </div>

        <!-- Külső link vagy almenü -->
        <a
            v-if="item.url && item.visible !== false"
            :href="item.url"
            @click="itemClick($event, item)"
            :class="item.class"
            :target="item.target"
            tabindex="0"
        >
            <i :class="item.icon" class="layout-menuitem-icon"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
            <i class="pi pi-fw pi-angle-down layout-submenu-toggler" v-if="item.items"></i>
        </a>

        <!-- Belső Inertia link -->
        <NavLink
            v-else-if="item.to && item.visible !== false"
            @click="itemClick($event, item)"
            :href="item.to"
            :class="[item.class, { 'active-route2': isActive }]"
        >
            <i :class="item.icon" class="layout-menuitem-icon"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
            <i class="pi pi-fw pi-angle-down layout-submenu-toggler" v-if="item.items"></i>
        </NavLink>

        <Transition v-if="item.items && item.visible !== false" name="layout-submenu">
            <ul v-show="root ? true : isActiveMenu" class="layout-submenu">
                <app-menu-item
                    v-show="!child?.can || can([child.can])"
                    v-for="(child, i) in item.items"
                    :key="child" :index="i" :item="child"
                    :parentItemKey="itemKey"
                    :root="false"
                ></app-menu-item>
            </ul>
        </Transition>
    </li>
</template>

<style lang="scss">
.layout-menu-label {
    padding: 0.75rem 1rem;
    font-weight: bold;
    text-transform: uppercase;
    color: #888;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;

    .layout-menu-label-icon {
        font-size: 1rem;
    }
}

.layout-menu ul a.active-route2 {
    color: var(--primary-color, #0d6efd);
}
</style>
