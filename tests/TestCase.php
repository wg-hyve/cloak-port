<?php

declare(strict_types=1);

namespace ClockPort\Tests;

use CloakPort\Creator\GuardLoader;
use CloakPort\Creator\GuardType;
use CloakPort\Creator\ProxyGuard;
use CloakPort\GuardServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use KeycloakGuard\Exceptions\InvalidTokenException;
use KeycloakGuard\Exceptions\ResourceAccessNotAllowedException;
use KeycloakGuard\KeycloakGuard;
use KeycloakGuard\Tests\Models\User;
use ClockPort\Tests\Traits\HasPayload;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use HasPayload;

    public function setUp(): void
    {
        parent::setUp();

        $this->app->setBasePath(__DIR__);
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('keycloak.token_principal_attribute', 'azp');
        $app['config']->set('keycloak.realm_public_key', $this->load('keys/public_no_wrap.key'));
        $app['config']->set('keycloak.allowed_resources', 'client-role-test');
        $app['config']->set('keycloak.service_role', 'client-role-test');
        $app['config']->set('keycloak.ignore_resources_validation', true);

        $app['config']->set('cloak_n_passport.keycloak_key_identifier', [
            'azp',
            'realm_access',
            'resource_access'
        ]);
        $app['config']->set('cloak_n_passport.guards', [
            'passport_user',
            'passport_client',
            'keycloak',
            'default'
        ]);
        $app['config']->set('cloak_n_passport.factory', GuardType::class);

//        $app['config']->set('auth.defaults.guard', 'api');
//        $app['config']->set('auth.providers.users.model', User::class);

//        $app['config']->set('auth.guards.cloak', [
//            'driver' => 'keycloak',
//            'provider' => 'users'
//        ]);

//        Http::fake(['keycloak.dev/auth/realms/testing' => Http::response(['public_key' => $this->load('keys/public_no_wrap.key')]),]);
//        Http::fake(['keycloak.dev/auth/realms/nope' => Http::response(['public_key' => null]), 404]);
    }

    protected function getPackageProviders($app): array
    {
//        Route::any('/acme/foo', [AcmeController::class, 'foo'])->middleware(['auth:cloak']);
//        Route::any('/acme/bar', [AcmeController::class, 'bar']);

        return [GuardServiceProvider::class,];
    }

    protected function withKeycloakToken(): static
    {
        $this->withToken($this->load('tokens/access_token'));

        return $this;
    }

    protected function withExpiredKeycloakToken(): static
    {
        $this->withToken($this->load('tokens/access_token_has_expire'));

        return $this;
    }

    protected function getKeycloakGuard(): ProxyGuard
    {
        $req = new Request();

//        var_dump($this->load('tokens/access_token'));

        $req->headers->set('Authorization', sprintf('Bearer %s', $this->load('tokens/access_token')));

        $config = [
            'driver' => 'keycloak_passport',
            'provider' => 'users',
            'request' => $req
        ];

        return GuardLoader::load($config);
    }
}