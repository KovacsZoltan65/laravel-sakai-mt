<?php

namespace App\Http\Controllers\Hq;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Tenants\Employee;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function response;

class TenantController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Hq/Dashboard', [
            'title' => 'Tenant Dashboard'
        ]);
    }

    public function index(Request $request)
    {
        return Inertia::render('Hq/Tenant/Index', [
            'title' => 'Tenants',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $page = $request->input('page', 1);

        try {
            $_tenants = Tenant::query();

            //$_tenants->withoutHq();

            if( $request->has('search') ) {
                $_tenants->whereRaw("CONCAT( name, ' ', domain) LIKE '%{$request->get('search')}%'");
            }

            if( $request->has('field') && $request->has('order') ) {
                $_tenants->orderBy($request->get('field'), $request->get('order'));
            }

            $tenants = $_tenants->paginate(10, ['*'], 'page', $page);

            return response()->json($tenants, Response::HTTP_OK);

        } catch( Exception $ex ) {
            \Log::info('TenantController fetch $ex message: ' . print_r($ex->getMessage(), true));
        }
    }

    public function storeTenant(Request $request)
    {
        try {
            $this->runTenantSetupViaArtisan($request);
        } catch( \Throwable $ex ) {
            \Log::error('storeTenant hiba: ' . $ex->getMessage());
            return response()->json(['error' => 'Hiba a tenant létrehozás során'], 500);
        }
        
        /*
        try {
            $newTenant = DB::transaction(function() use($request) {
                // 1. Tenant létrehozása
                $_tenant = Tenant::create($request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_tenant);

                // 3. Cache törlése, ha releváns

                return $_tenant;
            });

            return response()->json($newTenant, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'storeTenant ModelNotFoundException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'storeTenant Tenant not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'storeTenant QueryException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'storeTenant Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'storeTenant Exception: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'storeTenant Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        */
    }

    public function updateTenant(Request $request, int $id)
    {
        try {
            $updatedTenant = DB::transaction(function() use($request, $id) {
                $_tenant = Tenant::lockForUpdate()->findOrFail($id);
                // 1. Módosítandó rekord lekérése és zárolása
                $_tenant = Tenant::lockForUpdate()->findOrFail($id);
                
                // 🔒 Eredeti értékek mentése
                $original = $_tenant->only(['database', 'username', 'password', 'host']);
                
                // 2. Rekord frissítése
                $_tenant->update($request->all());
                
                // 3. Model frissítése
                $_tenant->refresh();
                
                // 🔁 Ha változott valami fontos, alkalmazzuk a MySQL-ben is
                $changed = array_filter([
                    'database' => $original['database'] !== $_tenant->database,
                    'username' => $original['username'] !== $_tenant->username,
                    'password' => $original['password'] !== $_tenant->password,
                    'host'     => $original['host']     !== $_tenant->host,
                ]);
                
                if (!empty($changed)) {
                    $this->applyMysqlChanges($_tenant, $original);
                }
                
                // 4. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_tenant);
                
                // 5. Cache törlése, ha releváns
                
                return $_tenant;
            });
            
            return response()->json($updatedTenant, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'updateTenant ModelNotFoundException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'updateTenant Employee not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'updateTenant QueryException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'updateTenant Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'updateTenant Exception: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'updateTenant Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteTenants(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1', // Kötelező, legalább 1 id kell
                'ids.*' => 'integer|exists:tenants,id', // Az id-k egész számok és létező cégek legyenek
            ]);

            $ids = $validated['ids'];

            $deletedCount = DB::transaction(function() use($ids): int {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $count = Employee::lockForUpdate()->whereIn('id', $ids)->delete();

                // b) Observer-kompatibilis, egyenkénti törlés:
                //$_cities = City::whereIn('id', $ids)->lockForUpdate()->get();
                //$_cities->each(function (City $city) use (&$deletedCount) {
                //    if ($city->delete()) {
                //        $deletedCount++;
                //    }
                //});

                // Cache törlése, ha szükséges

                return $count;
            });

            return response()->json($deletedCount, Response::HTTP_OK);

        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'deleteTenants ModelNotFoundException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'deleteTenants Tenants not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'deleteTenants QueryException: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'deleteTenants Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'deleteTenants Exception: ' . print_r($ex->getMessage(), true));
            return response()->json(['error' => 'deleteTenants Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restoretenant(Request $request)
    {
        //
    }

    public function getTenantsToSelect(): array
    {
        // Get all tenants
        $tenants = Tenant::ToSelect();

        return $tenants;
    }

    private function applyMysqlChanges(Tenant $tenant, array $original): void
    {
        $newDb = $tenant->database;
        $newUser = $tenant->username;
        $newPass = $tenant->password;
        $newHost = $tenant->host;
        
        $oldDb = $original['database'];
        $oldUser = $original['username'];
        $oldPass = $original['password'];
        $oldHost = $original['host'];
        
        $db = DB::connection('landlord');
        
        // 1️⃣ Ha változott az adatbázisnév
        if ($oldDb !== $newDb) {
            $db->statement("RENAME DATABASE `$oldDb` TO `$newDb`");
        }
        
        // 2️⃣ Ha változott a felhasználónév vagy host
        if ($oldUser !== $newUser || $oldHost !== $newHost) {
            $db->statement("DROP USER IF EXISTS '$oldUser'@'$oldHost'");
            $db->statement("CREATE USER '$newUser'@'$newHost' IDENTIFIED WITH sha256_password BY '$newPass'");
        } elseif ($oldPass !== $newPass) {
            $db->statement("ALTER USER '$newUser'@'$newHost' IDENTIFIED WITH sha256_password BY '$newPass'");
        }
        
        // 3️⃣ Jogosultság beállítása
        $db->statement("GRANT ALL PRIVILEGES ON `$newDb`.* TO '$newUser'@'$newHost'");
        $db->statement("FLUSH PRIVILEGES");
        
        \Log::info("✅ MySQL jogok és módosítások alkalmazva tenant: {$tenant->name}");
    }
    
    private function runTenantSetupViaArtisan(Request $request): void
    {
        Artisan::call('tenant:setup', [
            'name' => $request->input('name'),
            'domain' => $request->input('domain'),
            'database' => $request->input('database'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            '--host' => $request->input('host', 'localhost'),
            '--active' => $request->input('active', 1),
            '--seeder' => 'DatabaseSeeder', // vagy tenant-specifikus
        ]);

        \Log::info("Artisan setup parancs futott: \n" . Artisan::output());
    }
    
    private function createDefaultSettings(Tenant $tenant): void
    {
        /*
        $username = $tenant->username;
        $password = $tenant->password;
        $database = $tenant->database;
        //$host = 'localhost'; // vagy '%' ha távoli hozzáférés is kell
        $host = $tenant->host;
    
        // 1. CREATE USER
        $query_1 = "CREATE USER IF NOT EXISTS '$username'@'$host' IDENTIFIED WITH sha256_password BY '$password'";
\Log::info($query_1);
        DB::connection('landlord')->statement($query_1);

        
        // 2. GRANT USAGE
        $query_2 = "GRANT USAGE ON *.* TO '$username'@'$host'";
\Log::info($query_2);
        DB::connection('landlord')->statement($query_2);
        
        // 3. ALTER USER LIMITS
        $query_3 = "ALTER USER '$username'@'$host' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
\Log::info($query_3);
        DB::connection('landlord')->statement($query_3);
        
        // 4. CREATE DATABASE
        $query_4 = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci";
\Log::info($query_4);
        DB::connection('landlord')->statement($query_4);
        
        // 5. GRANT PRIVILEGES ON NEW DATABASE
        $query_5 = "GRANT ALL PRIVILEGES ON `$database`.* TO '$username'@'$host'";
\Log::info($query_5);
        DB::connection('landlord')->statement($query_5);
        */
        /*
        // 6. FLUSH PRIVILEGES (biztonság kedvéért)
        DB::connection('landlord')->statement("FLUSH PRIVILEGES");
        
        // 7. ELLENŐRZÉS: adatbázis létezik?
        $dbExists = DB::connection('landlord')->selectOne("
            SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?
        ", [$database]);
        if (!$dbExists) {
            throw new \Exception("Az adatbázis nem jött létre: `$database`");
        }
        
        // 8. ELLENŐRZÉS: felhasználó létezik?
        $userExists = DB::connection('landlord')->selectOne("
            SELECT User FROM mysql.user WHERE User = ? AND Host = ?", [$username, $host]
        );
        if (!$userExists) {
            throw new \Exception("A MySQL felhasználó nem jött létre: `$username@$host`");
        }
        
        // 9. Opcionális: jogosultság ellenőrzése
        // Megnézhetjük, van-e jogosultság, pl. SHOW GRANTS FOR...
        $grants = DB::connection('landlord')->select("SHOW GRANTS FOR '$username'@'$host'");
        $hasGrant = collect($grants)->flatten()->contains(function ($grant) use ($database) {
            return str_contains($grant, "`$database`.*") && str_contains($grant, 'ALL PRIVILEGES');
        });

        if (!$hasGrant) {
            throw new \Exception("A felhasználó nem kapta meg a megfelelő jogosultságokat a(z) `$database` adatbázisra.");
        }

        // ✅ Sikeres létrehozás
        \Log::info("Tenant adatbázis sikeresen létrehozva: $database | Felhasználó: $username@$host");
        */
    }

    private function updateDefaultSettings(Tenant $tenant): void
    {
        //
    }
}
