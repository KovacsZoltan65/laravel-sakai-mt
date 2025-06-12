import { computed, reactive, readonly } from "vue";

const layoutConfig = reactive({
    preset: "Aura",
    primary: "emerald",
    surface: null,
    darkTheme: false,
    menuMode: "static",
});

const layoutState = reactive({
    staticMenuDesktopInactive: false,
    overlayMenuActive: false,
    profileSidebarVisible: false,
    configSidebarVisible: false,
    staticMenuMobileActive: false,
    menuHoverActive: false,
    activeMenuItem: null,
});

export function useLayout() {
    const setPrimary = (value) => {
        layoutConfig.primary = value;
    };

    const setSurface = (value) => {
        layoutConfig.surface = value;
    };

    const setPreset = (value) => {
        layoutConfig.preset = value;
    };

    const setActiveMenuItem = (item) => {
        layoutState.activeMenuItem = item.value || item;
    };

    const setMenuMode = (mode) => {
        layoutConfig.menuMode = mode;
    };

    /**
     * Bekapcsolja a sötét módot.
     *
     * Ha a böngésző nem támogatja a "startViewTransition" funkciót, akkor közvetlenül meghívja az "executeDarkModeToggle" parancsot.
     * Ellenkező esetben a `startViewTransition` segítségével késlelteti az `executeDarkModeToggle` végrehajtását.
     *
     * @return {void}
     */
    const toggleDarkMode = () => {
        // Ha a böngésző nem támogatja a "startViewTransition" funkciót, azonnal kapcsolja át a sötét módot.
        if (!document.startViewTransition) {
            executeDarkModeToggle();

            return;
        }

        // Ha a böngésző támogatja a "startViewTransition" funkciót, késleltesse az "executeDarkModeToggle" végrehajtását.
        document.startViewTransition(() => executeDarkModeToggle(event));
    };

    /**
     * Bekapcsolja a sötét módot.
     *
     * Ez a funkció a `layoutConfig` objektum `darkTheme` tulajdonságának értékének megváltoztatásával váltja át a sötét módot.
     * Ezenkívül hozzáadja vagy eltávolítja az „app-dark” osztályt a dokumentum gyökéreleméből, hogy tükrözze a sötét mód állapotát.
     *
     * @return {void}
     */
    const executeDarkModeToggle = () => {
        // Váltsa át a sötét téma értékét az elrendezés konfigurációjában
        layoutConfig.darkTheme = !layoutConfig.darkTheme;

        // Kapcsolja be az „app-dark” osztályt a dokumentum gyökérelemén
        // a sötét mód stílusának alkalmazásához vagy eltávolításához
        document.documentElement.classList.toggle("app-dark");
    };

    /**
     * Váltsa a menü állapotát az aktuális menümód alapján.
     * Ha a menü mód 'overlay', akkor átkapcsolja az overlay menü állapotát.
     * Ha az ablak szélessége nagyobb, mint 991 pixel, átkapcsolja a statikus menü állapotát az asztalon.
     * Ellenkező esetben átkapcsolja a mobil menü statikus állapotát.
     *
     * @return {void}
     */
    const onMenuToggle = () => {
        // Váltsa át az átfedő menü állapotát, ha a menü mód "overlay"
        if (layoutConfig.menuMode === "overlay") {
            layoutState.overlayMenuActive = !layoutState.overlayMenuActive;
        }

        // Váltsa át a statikus menü állapotát az asztalon, ha az ablak szélessége nagyobb, mint 991 pixel
        if (window.innerWidth > 991) {
            layoutState.staticMenuDesktopInactive =
                !layoutState.staticMenuDesktopInactive;
        }
        // Ellenkező esetben kapcsolja át a statikus menü állapotát a mobilhoz
        else {
            layoutState.staticMenuMobileActive =
                !layoutState.staticMenuMobileActive;
        }
    };

    /**
     * Visszaállítja a menü állapotát az alapértelmezett értékekre.
     *
     * Az overlay menü, a mobil menü és a menü gyökér (hover) állapotát nullázja.
     *
     * @return {void}
     */
    const resetMenu = () => {
        // Visszaállítja az overlay menü állapotát az alapértelmezett értékre (false)
        layoutState.overlayMenuActive = false;

        // Visszaállítja a mobil menü állapotát az alapértelmezett értékre (false)
        layoutState.staticMenuMobileActive = false;

        // Visszaállítja a menü gyökér (hover) állapotát az alapértelmezett értékre (false)
        layoutState.menuHoverActive = false;
    };

    /**
     * Ellenőrzi, hogy az oldalsáv aktív-e.
     * Az oldalsáv akkor tekinthető aktívnak, ha az átfedő menü vagy a mobil menü aktív.
     *
     * @return {boolean} - Igaz értéket ad vissza, ha az oldalsáv aktív, hamis értéket egyébként.
     */
    const isSidebarActive = computed(() => {
        // Ellenőrizze, hogy az átfedő menü vagy a mobil menü aktív-e.
        // Ha bármelyik aktív, az oldalsáv aktívnak minősül.
        return (
            layoutState.overlayMenuActive || layoutState.staticMenuMobileActive
        );
    });

    /**
     * Egy számított tulajdonságot ad vissza, amely a sötét téma állapotát képviseli.
     * Ez a számított tulajdonság egy logikai érték, amely jelzi, hogy a sötét téma engedélyezve van-e vagy sem.
     *
     * @return {boolean} - Logikai érték, amely igaz, ha a sötét téma engedélyezve van, egyébként false.
     */
    const isDarkTheme = computed(() => {
        // Visszaadja a sötét téma állapotát az elrendezés konfigurációs objektumból.
        // A sötét téma állapotát a 'layoutConfig' objektum 'darkTheme' tulajdonsága tárolja.
        return layoutConfig.darkTheme;
    });

    /**
     * Egy számított tulajdonságot ad vissza, amely az elrendezési konfiguráció elsődleges színét képviseli.
     *
     * @return {string} Az elrendezés konfigurációjának elsődleges színe.
     */
    const getPrimary = computed(() => {
        // Szerezze le az elsődleges színértéket az elrendezés konfigurációs objektumából.
        // Az elsődleges szín a 'layoutConfig' objektum 'primary' tulajdonságában van tárolva.
        return layoutConfig.primary;
    });

    /**
     * Egy számított tulajdonságot ad vissza, amely az elrendezési konfiguráció felületét képviseli.
     *
     * @return {string | null} Az elrendezés konfigurációjának felületét (surface) vagy null értéket, ha nincs beállítva.
     */
    const getSurface = computed(() => {
        // Szerezze le az elrendezés konfigurációjának felületét (surface) vagy null értéket, ha nincs beállítva.
        // Az elrendezés konfigurációs objektum 'surface' tulajdonságában van tárolva.
        return layoutConfig.surface;
    });

    /**
     * A számított tulajdonságok objektuma, amely a layout konfigurációs és állapotát jelenti.
     * Az objektum olvasható (readonly) és a számított tulajdonságok segítségével az ügyféloldali kód
     * a layout konfigurációs és állapotának változásait követi figyelemmel.
     *
     * @return {Object} - A layout konfigurációs és állapotának számított tulajdonságai.
     */
    return {
        // Olvasható (readonly) elrendezés konfigurációs objektum.
        layoutConfig: readonly(layoutConfig),
        // Olvasható (readonly) elrendezés állapotának objektum.
        layoutState: readonly(layoutState),
        // A menü megnyitását vagy bezárását kezelő függvény.
        onMenuToggle,
        // Egy számított tulajdonság, amely jelzi, hogy az oldalsáv aktív-e vagy sem.
        isSidebarActive,
        // Egy számított tulajdonság, amely jelzi, hogy a sötét téma engedélyezve van-e vagy sem.
        isDarkTheme,
        // Egy számított tulajdonság, amely az elrendezési konfiguráció elsődleges színét képviseli.
        getPrimary,
        // Egy számított tulajdonság, amely az elrendezési konfiguráció felületét képviseli.
        getSurface,
        // Az aktív menüpont beállítására szolgáló függvény.
        setActiveMenuItem,
        // A sötét téma bekapcsolására vagy kikapcsolására szolgáló függvény.
        toggleDarkMode,
        // Az elsődleges szín beállítására szolgáló függvény.
        setPrimary,
        // A felület (surface) beállítására szolgáló függvény.
        setSurface,
        // Az elrendezés beállítására szolgáló függvény.
        setPreset,
        // A menü állítására szolgáló függvény.
        resetMenu,
        // A menü mód beállítására szolgáló függvény.
        setMenuMode,
    };
}
