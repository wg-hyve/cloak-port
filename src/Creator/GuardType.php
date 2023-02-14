<?php

namespace CloakPort\Creator;

use CloakPort\GuardContract;
use CloakPort\GuardTypeContract;
use CloakPort\TokenGuard as DefaultGuard;
use Illuminate\Support\Facades\Auth;
use CloakPort\Keycloak\TokenGuard as KeycloakGuard;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\PassportUserProvider;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;
use CloakPort\Passport\TokenClientGuard as PassportClientGuard;
use CloakPort\Passport\TokenUserGuard as PassportUserGuard;

enum GuardType implements GuardTypeContract
{
    case KEYCLOAK;
    case PASSPORT_CLIENT;
    case PASSPORT_USER;
    case DEFAULT;

    public static function load(string $backend): self
    {
        return match(strtolower($backend)) {
            'keycloak' => GuardType::KEYCLOAK,
            'passport_client' => GuardType::PASSPORT_CLIENT,
            'passport_user' => GuardType::PASSPORT_USER,
            default => GuardType::DEFAULT
        };
    }

    public function loadFrom(array $config): GuardContract
    {
        return match ($this) {
            self::KEYCLOAK => new KeycloakGuard(Auth::createUserProvider($config['provider']), request()),

            self::PASSPORT_CLIENT => new PassportClientGuard(
                app()->make(ResourceServer::class),
                new PassportUserProvider(Auth::createUserProvider($config['provider']), $config['provider']),
                app()->make(TokenRepository::class),
                app()->make(ClientRepository::class),
                app()->make('encrypter'),
                app()->make('request')
            ),

            self::PASSPORT_USER => new PassportUserGuard(
                app()->make(ResourceServer::class),
                new PassportUserProvider(Auth::createUserProvider($config['provider']), $config['provider']),
                app()->make(TokenRepository::class),
                app()->make(ClientRepository::class),
                app()->make('encrypter'),
                app()->make('request')
            ),

            self::DEFAULT => new DefaultGuard()
        };
    }
}
