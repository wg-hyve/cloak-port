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
use CloakPort\Passport\TokenGuard as PassportGuard;

enum GuardType implements GuardTypeContract
{
    case KEYCLOAK;
    case PASSPORT;
    case DEFAULT;

    public static function load(string $backend): self
    {
        return match(strtolower($backend)) {
            'keycloak' => GuardType::KEYCLOAK,
            'passport' => GuardType::PASSPORT,
            default => GuardType::DEFAULT
        };
    }

    public function guardClass(): string
    {
        return match ($this) {
            self::KEYCLOAK => KeycloakGuard::class,
            self::PASSPORT => PassportGuard::class,
            self::DEFAULT => DefaultGuard::class,
        };
    }

    public function loadFrom(array $config): GuardContract
    {
        return match ($this) {
            self::KEYCLOAK => new KeycloakGuard(Auth::createUserProvider($config['provider']), request()),

            self::PASSPORT => new PassportGuard(
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
