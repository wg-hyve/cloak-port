<?php

declare(strict_types=1);

namespace ClockPort\Tests;

use CloakPort\Creator\GuardLoader;
use CloakPort\Creator\GuardType;
use CloakPort\Creator\ProxyGuard;
use CloakPort\GuardServiceProvider;
use ClockPort\Tests\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use KeycloakGuard\Exceptions\InvalidTokenException;
use KeycloakGuard\Exceptions\ResourceAccessNotAllowedException;
use KeycloakGuard\KeycloakGuard;
use ClockPort\Tests\Traits\HasPayload;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Laravel\Passport\Client;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\ResourceServer;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use HasPayload;

    public function setUp(): void
    {
        parent::setUp();

        $this->app->setBasePath(__DIR__);

        $this->setUpDatabase($this->app);
        $this->seedDb();
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     * @return void
     * @throws BindingResolutionException
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:DLrHy+DjRdWYWjVU4meO/6yOqWaPVPmxlWRWoFLnxZY=');
        $app['config']->set('passport.public_key', $this->load('keys/passport-public.key'));
        $app['config']->set('passport.private_key', $this->load('keys/passport-private.key'));

        $app['config']->set('keycloak.token_principal_attribute', 'azp');
        $app['config']->set('keycloak.realm_public_key', $this->load('keys/public_no_wrap.key'));
        $app['config']->set('keycloak.allowed_resources', 'client-role-test');
        $app['config']->set('keycloak.service_role', 'client-role-test');
        $app['config']->set('keycloak.provide_user', false);
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

        $app['config']->set('auth.providers.users.model', User::class);

        $app->singleton(ResourceServer::class, function ($container) {
            return new ResourceServer(
                app()->make(AccessTokenRepository::class),
                $this->makeCryptKey('public')
            );
        });
    }

    protected function getPackageProviders($app): array
    {
        return [GuardServiceProvider::class,];
    }

    protected function setUpDatabase(Application $app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('oauth_access_tokens', function (Blueprint $table) {
            $table->string('id');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('oauth_clients', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->timestamps();
        });
    }

    protected function seedDb()
    {
        Token::create(['id' => '97fef3a83ee60e89ec7e84ca3a6c8bfd4d5c846c25103631fae4ac4e911cc0e20593bdb8d743de52']);
        Token::create(['id' => '96e6ed779b7392efcd38e5a2d59e252b5584df981743c0a2a0e7f96ffb0216a77f08b19ce8787d11']);
        Client::create(['id' => 'd03a0c03-354e-43b7-a7f2-aae4ed39a190']);
        Client::create(['id' => 'efc58327-30d4-472c-996d-7901f8fdeb69']);
        User::create(['username' => 'John Doe']);
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

        $req->headers->set('Authorization', sprintf('Bearer %s', $this->load('tokens/access_token')));
        $req->headers->set('HOST', 'example.com');
        
        $config = [
            'driver' => 'keycloak_passport',
            'provider' => 'users',
            'request' => $req
        ];

        return GuardLoader::load($config);
    }

    protected function getPassportClientGuard(): ProxyGuard
    {
        $req = new Request();

        $req->headers->set('Authorization', sprintf('Bearer %s', $this->load('tokens/access_token_passport_client')));
        $req->headers->set('HOST', 'example.com');

        $config = [
            'driver' => 'keycloak_passport',
            'provider' => 'users',
            'request' => $req
        ];
        
        return GuardLoader::load($config);
    }

    protected function getPassportUserGuard(): ProxyGuard
    {
        $req = new Request();

        $req->headers->set('Authorization', sprintf('Bearer %s', $this->load('tokens/access_token_passport_user')));
        $req->headers->set('HOST', 'example.com');

        $config = [
            'driver' => 'keycloak_passport',
            'provider' => 'users',
            'request' => $req
        ];

        return GuardLoader::load($config);
    }

    protected function getDefaultGuard(): ProxyGuard
    {
        $req = new Request();

        $req->headers->set('Authorization', sprintf('Bearer %s', $this->load('tokens/access_token_default_guard')));
        $req->headers->set('HOST', 'example.com');

        $config = [
            'driver' => 'keycloak_passport',
            'provider' => 'users',
            'request' => $req
        ];

        return GuardLoader::load($config);
    }

    protected function makeCryptKey($type): CryptKey
    {
        $key = str_replace('\\n', "\n", config('passport.'.$type.'_key') ?? '');

        if (! $key) {
            $key = 'file://'.Passport::keyPath('oauth-'.$type.'.key');
        }

        return new CryptKey($key, null, false);
    }
}