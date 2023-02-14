<?php

namespace CloakPort;

use CloakPort\Creator\GuardLoader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class GuardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/config/cloak_n_passport.php' => config_path('cloak_n_passport.php'),
            ],
            'cloak-port-config'
        );

        $this->mergeConfigFrom(__DIR__ . '/config/cloak_n_passport.php', 'cloak_n_passport');
    }

    public function register()
    {
        Auth::extend('keycloak_passport', function ($app, $name, array $config) {
            return GuardLoader::load($config);
        });
    }
}
