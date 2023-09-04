<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Setting;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        resolve(UrlGenerator::class)->forceScheme('https');
        view()->composer('asbab.*', function($view) {
            $cart = session()->get('cart');
            $brands = Brand::all();
            $settings = Setting::all();
            $view->with('carts', $cart)->with('brands', $brands)->with('settings', $settings);
        });
    }
}
