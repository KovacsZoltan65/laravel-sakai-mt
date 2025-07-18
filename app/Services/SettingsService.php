<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\CompanySetting;
use App\Models\UserSetting;
use App\Models\SettingsMeta;

class SettingsService
{
    public function resolve(string $key, ?int $userId = null, ?int $companyId = null): array
    {
        /**
         * Oldja fel a beállítási értéket a megadott kulccsal.
         *
         * A beállítások értéke a következő sorrendben oldódik fel:
         * 1. Felhasználói beállítás
         * 2. Vállalati beállítás
         * 3. Alkalmazásbeállítás
         * 4. Alapértelmezett érték a BeállításokMeta alapján
         *
         * @param string $key
         * @param int|null $userId
         * @param int|null $companyId
         * @return array{
         *     key: string,
         *     value: mixed,
         *     source: string|null,
         *     active: bool,
         *     active_when: array<string, mixed>|null,
         *     dependencies: array<string>,
         *     resolved_dependencies: array<string, mixed>
         * }
         */
        $meta = SettingsMeta::where('key', $key)->first();

        // Check if meta information is available for the given key
        if (!$meta) {
            // Return a default response indicating inactive status with no value or dependencies
            return [
                'key' => $key,
                'value' => null,               // Egyetlen érték sem oldható fel
                'source' => null,              // Nincs értékforrás
                'active' => false,             // A beállítás nem aktív
                'active_when' => null,         // Nincs elérhető active_when logika
                'dependencies' => [],          // Nincsenek függőségek
                'resolved_dependencies' => [], // Nincsenek feloldott függőségek
                'meta' => null,                // Nincs metaadat
            ];
        }

        // Lekérjük a függőségeket a BeállításokMeta-ból
        $dependencies = collect($meta->dependencies ?? []);
        // Azok a függőségek, amelyek már fel vannak oldva.
        // Azokat a beállításokat, amelyek a függőségekhez tartoznak, itt tároljuk.
        $resolvedDeps = [];

        // Lekérjük a függőségek értékeit
        foreach ($dependencies as $depKey) {
            $resolvedDeps[$depKey] = self::get($depKey, $userId, $companyId);
        }

        // Aktív-e a beállítás?
        $isActive = true;
        // Ha van active_when logika, kiértékeljük
        if ($meta->active_when) {
            $activeRules = collect($meta->active_when);
            // A függőségek értékét vizsgálja és igaz, ha minden függőség értéke megegyezik
            // a beállított értékkel
            $isActive = $activeRules->every(function ($expected, $depKey) use ($resolvedDeps) {
                // Ha nincs definiálva a függőség értéke, akkor nem aktív
                if (!isset($resolvedDeps[$depKey])) {
                    return false;
                }
                // Ellenkező esetben a függőség értékét vizsgáljuk
                return $resolvedDeps[$depKey] == $expected;
            });
        }

        // Érték és forrás lekérése, ha a beállítás aktív
        $value = $isActive ? self::get($key, $userId, $companyId) : null;
        // Forrás lekérése, ha a beállítás aktív
        // A forrás az a hely, ahol a beállítás értéke származik
        // (felhasználói beállítás, vállalati beállítás, alkalmazásbeállítás, vagy alapértelmezett érték)
        $source = $isActive ? self::getSource($key, $userId, $companyId) : null;

        /**
         * A feloldott beállítást tömbként adja vissza.
         *
         * @return array{
         *     key: string,
         *     value: mixed,
         *     source: string|null,
         *     active: bool,
         *     active_when: array<string, mixed>|null,
         *     dependencies: array<string>,
         *     resolved_dependencies: array<string, mixed>,
         *     meta: SettingsMeta|null
         * }
         */
        return [
            'key' => $key,                            // A beállítás kulcsa
            'value' => $value,                        // A feloldott érték, vagy null, ha inaktív
            'source' => $source,                      // Az érték forrása, vagy null, ha inaktív
            'active' => $isActive,                    // A beállítás aktív-e
            'active_when' => $meta->active_when,      // A beállítás aktiválásának feltételei
            'dependencies' => $dependencies,          // A beállítás függőségei
            'resolved_dependencies' => $resolvedDeps, // Feloldott függőségi értékek
            'meta' => $meta,                          // A beállítás metaadatai
        ];
    }

    public static function get(string $key, ?int $userId = null, ?int $companyId = null)
    {
        // 1. UserSetting
        if ($userId) {
            $userSetting = UserSetting::where('user_id', $userId)->where('key', $key)->first();
            if ($userSetting) {
                return $userSetting->value;
            }
        }

        // 2. CompanySetting
        if ($companyId) {
            $companySetting = CompanySetting::where('company_id', $companyId)->where('key', $key)->first();
            if ($companySetting) {
                return $companySetting->value;
            }
        }

        // 3. AppSetting
        $appSetting = AppSetting::where('key', $key)->first();
        if ($appSetting) {
            return $appSetting->value;
        }

        // 4. Default érték a metából
        $meta = SettingsMeta::where('key', $key)->first();

        return $meta?->default_value;
    }

    /**
     * Visszaadja a beállítás értékét figyelembe véve a függőségeket is.
     *
     * Ha van active_when logika, akkor ezt is kiértékeli, és ha nem teljesül, akkor
     * egy olyan tömböt ad vissza, aminek az 'active' eleme false, a 'value' pedig null.
     *
     * A függőségek értékét is lekéri, hogy ne kelljen duplán lekérni.
     *
     * @param string $key
     * @param int|null $userId
     * @param int|null $companyId
     * @return array{
     *     active: bool,
     *     value: mixed,
     *     source: string|null
     * }
     */
    public static function getWithContext(string $key, ?int $userId = null, ?int $companyId = null)
    {
        $meta = SettingsMeta::where('key', $key)->first();
        if( !$meta ) {
            return [
                'active' => false,
                'value' => null,
                'source' => null,
            ];
        }

        $dependencies = collect($meta->dependencies ?? []);
        $dependencyValues = [];

        // Lekérjük a függőségek értékét is
        foreach ($dependencies as $depKey) {
            $dependencyValues[$depKey] = self::get($depKey, $userId, $companyId);
        }

        // Ha van active_when logika, kiértékeljük
        if ($meta->active_when) {
            $activeRules = collect($meta->active_when); // pl. ['enable_theme' => true]

            // A függőségek értékét vizsgálja és igaz, ha minden függőség értéke megegyezik
            // a beállított értékkel
            $isActive = $activeRules->every(function ($expectedValue, $depKey) use ($dependencyValues) {
                // Ha nincs definiálva a függőség értéke, akkor nem aktív
                if (!isset($dependencyValues[$depKey])) {
                    return false;
                }
                // Ellenkező esetben a függőség értékét vizsgáljuk
                return $dependencyValues[$depKey] == $expectedValue;
            });

            // Ha nem teljesül a függőség logika, akkor nem aktív
            // Ekkor egy olyan tömböt adunk vissza, aminek az 'active' eleme false,
            // a 'value' pedig null.
            if (!$isActive) {
                return [
                    'active' => false,
                    'value' => null,
                    'source' => null,
                ];
            }
        }

        // Érték meghatározása a megszokott módon
        $value = self::get($key, $userId, $companyId);
        $source = self::getSource($key, $userId, $companyId);

        return [
            'active' => true,
            'value' => $value,
            'source' => $source,
        ];
    }

    /**
     * Megadja, hogy egy beállítás értéke melyik forrásból származik.
     *
     * @param string $key
     * @param int|null $userId
     * @param int|null $companyId
     * @return string|null 'user', 'company', 'app', 'default', vagy null, ha nincs ilyen kulcs
     */
    public static function getSource(string $key, ?int $userId = null, ?int $companyId = null): ?string
    {
        // Elsőként a user beállításokat vizsgáljuk
        if ($userId) {
            $userSetting = UserSetting::where('user_id', $userId)->where('key', $key)->first();
            if ($userSetting) {
                // Ha van ilyen kulcs a user beállításokban, akkor ez a forrás
                return 'user';
            }
        }

        // Cégspecifikus beállítások ellenőrzése
        if ($companyId) {
            // A megadott cég és kulcs beállításainak lekérése
            $companySetting = CompanySetting::where('company_id', $companyId)->where('key', $key)->first();

            // Ha létezik beállítás a céghez, akkor a „cég” értéket adja vissza forrásként.
            if ($companySetting) {
                return 'company';
            }
        }

        // A beállítások lekérése alkalmazás-szinten
        // Ha van ilyen kulcs az alkalmazásban, akkor ez a forrás
        $appSetting = AppSetting::where('key', $key)->first();

        // Ha van ilyen kulcs az alkalmazásban, akkor ez a forrás
        if ($appSetting)
            return 'app';

        // A megadott kulcs metaadatainak lekérése
        $meta = SettingsMeta::where('key', $key)->first();

        // Ellenőrizd, hogy a meta létezik-e, és van-e alapértelmezett értéke.
        if ($meta && $meta->default_value !== null) {
            // 'alapértelmezett' értéket ad vissza forrásként, ha alapértelmezett érték van beállítva
            return 'default';
        }

        return null; // nincs ilyen kulcs
    }
}
