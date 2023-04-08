<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;

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
        Filament::registerViteTheme('resources/css/filament.css');
        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                   ->label('Finance')
                   ->collapsed(),
                NavigationGroup::make()
                   ->label('Perceptions')
                   ->collapsed(),
                NavigationGroup::make()
                   ->label('General')
                   ->collapsed(),
            ]);
        });
    }
}
