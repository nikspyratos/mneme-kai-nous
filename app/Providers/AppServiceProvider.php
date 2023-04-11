<?php

declare(strict_types=1);

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
        Filament::serving(function () {
            Filament::registerViteTheme('resources/css/filament.css');
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
