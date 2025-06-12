<script setup>
import { onBeforeMount, ref, watch } from "vue";
import { useLayout } from "../Layouts/composables/layout";

const { layoutState, setActiveMenuItem, onMenuToggle } = useLayout();

/**
 * Kellékek az AppMenuItem összetevőhöz.
 * @typedef {Object} AppMenuItemProps
 * @property {Object} item - A menüpont objektuma.
 * @property {number} index - A menüpont indexe.
 * @property {boolean} root - Azt jelzi, hogy a menüpont gyökérmenü-e.
 * @property {string|null} parentItemKey - A szülő menüpont kulcsa.
 */

/**
 * Meghatározza az AppMenuItem összetevő kellékeit.
 *
 * @type {AppMenuItemProps}
 */
const props = defineProps({
    /**
     * A menüpont objektuma.
     * @type {Object}
     * @default {}
     */
    item: {
        type: Object,
        default: () => ({}),
    },
    /**
     * A menüpont indexe.
     * @type {number}
     * @default 0
     */
    index: {
        type: Number,
        default: 0,
    },
    /**
     * Azt jelzi, hogy a menüpont gyökérmenü-e.
     * @type {boolean}
     * @default true
     */
    root: {
        type: Boolean,
        default: true,
    },
    /**
     * A szülő menüpont kulcsa.
     * @type {string|null}
     * @default null
     */
    parentItemKey: {
        type: String,
        default: null,
    },
});

const isActiveMenu = ref(false);
const itemKey = ref(null);

/**
 * Ezt a függvényt a rendszer a komponens csatlakoztatása előtt hívja meg.
 * Az itemKey értéket a parentItemKey és az index kellékei alapján állítja be.
 * Az isActiveMenu értéket is beállítja a layoutState-ból származó activeMenuItem értéke alapján.
 */
onBeforeMount(() => {
    // Állítsa be az itemKey értéket a parentItemKey és az index kellékei alapján.
    // Ha a parentItemKey értéke null, használja az indexet itemKeyként.
    itemKey.value = props.parentItemKey
        ? props.parentItemKey + "-" + props.index
        : String(props.index);

    // Szerezze be az activeMenuItem értéket a layoutState-ből.
    const activeItem = layoutState.activeMenuItem;

    // Állítsa be az isActiveMenu értéket az activeItem értéke alapján.
    // Ha az activeItem egyenlő az itemKey értékkel, állítsa az isActiveMenu értéket igazra.
    // Ha az activeItem az itemKey karakterrel kezdődik, amelyet kötőjel követ, állítsa az isActiveMenu értéket igazra.
    // Ellenkező esetben állítsa az isActiveMenu-t false értékre.
    isActiveMenu.value =
        activeItem === itemKey.value || activeItem
            ? activeItem.startsWith(itemKey.value + "-")
            : false;
});

/**
 * A watcher függvény, amely figyeli a layoutState-ból származó activeMenuItem értékét.
 * Amennyiben az érték megváltozik, frissíti az isActiveMenu értékét.
 *
 * @param {string} newVal - az új activeMenuItem érték
 * @return {void}
 */
watch(
    // A figyelt érték
    () => layoutState.activeMenuItem,
    // A módosított értékkel hívódó függvény
    (newVal) => {
        // Amennyiben az activeMenuItem érték egyenlő az itemKey-val, vagy az itemKey karakterrel
        // kezdődik és kötőjel követ, állítsa az isActiveMenu értéket igazra.
        // Ha nem, állítsa azt false értékre.
        isActiveMenu.value =
            newVal === itemKey.value || newVal.startsWith(itemKey.value + "-");
    },
);

/**
 * A függvény a menüelem kattintásakor hívódik meg.
 * Ellenőrzi, hogy a kattintott elem nem letiltott, majd ha letiltott, megszakítja a kattintás feldolgozását.
 * Ellenőrzi, hogy a kattintott elem tartalmaz-e URL-t vagy URL-t és almenüelemeket.
 * Ha igen, akkor meghívódik a onMenuToggle függvény.
 * Ellenőrzi, hogy a kattintott elem tartalmaz-e parancsot.
 * Ha igen, akkor meghívódik a parancs és a kattintás adataival.
 * Végül meghatározza a kattintott elem kulcsát (itemKey) és meghívódik a setActiveMenuItem függvény.
 *
 * @param {Event} event - az esemény, amelyet a menüelem kattintás generált
 * @param {Object} item - a kattintott menüelem objektuma
 * @return {void}
 */
function itemClick(event, item) {
    // Ellenőrzi, hogy a kattintott elem nem letiltott,
    // majd megszakítja a kattintás feldolgozását.

    if (item.disabled) {
        event.preventDefault();
        return;
    }

    // Ellenőrzi, hogy a kattintott elem tartalmaz-e URL-t vagy URL-t és almenüelemeket.
    // Ha igen, akkor meghívódik a onMenuToggle függvény.
    if (
        (item.to || item.url) &&
        (layoutState.staticMenuMobileActive || layoutState.overlayMenuActive)
    ) {
        onMenuToggle();
    }

    // Ellenőrzi, hogy a kattintott elem tartalmaz-e parancsot.
    // Ha igen, akkor meghívódik a parancs és a kattintás adataival.
    if (item.command) {
        item.command({ originalEvent: event, item: item });
    }

    // Meghatározza a kattintott elem kulcsát.
    // Ha a kattintott elem almenüelemeket tartalmaz, akkor az itemKey alapján a parentItemKey-t használja.
    // Ha nem, akkor az itemKey-t használja.
    const foundItemKey = item.children && item.children.length > 0
        ? isActiveMenu.value
            ? props.parentItemKey
            : itemKey
        : itemKey.value;

    // Meghívódik a setActiveMenuItem függvény a kattintott elem kulcsával.
    setActiveMenuItem(foundItemKey);
}

/**
 * Ellenőrzi, hogy az aktuális útvonal megegyezik-e az adott elem útvonalával.
 *
 * @param {Object} item - Az útvonal ellenőrzéséhez szükséges elem.
 * @param {string} item.to - Az elem útvonala.
 * @return {boolean} - Igaz, ha az aktuális útvonal megegyezik az elem útvonalával, hamis egyébként.
 */
function checkActiveRoute(item) {
    // Ellenőrizze, hogy az aktuális útvonal megegyezik-e az adott elem útvonalával.
    // Igazat ad vissza, ha egyeznek, hamis értéket egyébként.
    // Az összehasonlítás annak ellenőrzésével történik, hogy az aktuális útvonal útvonala megegyezik-e az elem útvonalával.
    return route.path === item.to;
}
</script>

<template>
    <li
        :class="{
            'layout-root-menuitem': root,
            'active-menuitem': isActiveMenu,
        }"
    >
        <div
            v-if="root && item.visible !== false"
            class="layout-menuitem-root-text"
        >
            {{ $t(item.label) }}
        </div>

        <a
            v-if="item.visible !== false"
            :href="item.to || item.url"
            @click="itemClick($event, item, index)"
            :class="item.class"
            :target="item.target"
            tabindex="0"
        >
            <i :class="item.icon" class="layout-menuitem-icon"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
            <i
                class="pi pi-fw pi-angle-down layout-submenu-toggler"
                v-if="item.children && item.children.length > 0"
            ></i>
        </a>

        <Transition
            v-if="item.children && item.children.length > 0 && item.visible !== false"
            name="layout-submenu"
        >
            <ul v-show="root ? true : isActiveMenu" class="layout-submenu">
                <AppMenuItem
                    v-for="(child, i) in item.children"
                    :key="child"
                    :index="i"
                    :item="child"
                    :parentItemKey="itemKey"
                    :root="false"
                ></AppMenuItem>
            </ul>
        </Transition>
    </li>
</template>
