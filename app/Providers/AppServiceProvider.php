<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Đăng ký Gloudemans Shoppingcart provider
        $this->app->register(\Gloudemans\Shoppingcart\ShoppingcartServiceProvider::class);

        // Đăng ký alias cho Cart
        $loader = AliasLoader::getInstance();
        $loader->alias('Cart', \Gloudemans\Shoppingcart\Facades\Cart::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
