<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(125);

        define('APP_ACTIVE', 1);
        define('APP_INACTIVE', 0);

        define('APP_TRUE', true);
        define('APP_FALSE', false);

        $available_locales = config('app.available_locales', ['English' => 'en','Hungarian' => 'hu',]);
        $supported_locales = config('app.supported_locales', ['en', 'hu']);
        $locale = ( Session::has('locale') ) ? Session::get('locale') : env('APP_LOCALE');

        Inertia::share([
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'available_locales' => $available_locales,
            'supported_locales' => $supported_locales,
            'locale' => $locale,
            // 🆕 Kiválasztott cég globálisan
            'selected_company' => fn () => Session::has('selected_company_id')
                ? \App\Models\Company::select('id', 'name')->find(Session::get('selected_company_id'))
                : null,
        ]);

        Inertia::share('flash', function(){
            return [
                'message' => Session::get('message'),
            ];
        });

        Inertia::share('csrf_token', function(){
            return csrf_token();
        });
        
        /*
        Inertia::share('selected_company', function() {
            $companyId = Session::get('selected_company_id');
            
            if( !$companyId ) {
                return null;
            }
            
            // Ha szükséges, cache-elve is betöltheted (opcionális teljesítmény okokból)
            return \App\Models\Company::select('id', 'name')->find($companyId);
        });
        */

    }
}
