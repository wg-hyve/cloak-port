<?php

namespace CloakPort\Creator;

use CloakPort\GuardContract;
use CloakPort\GuardTypeContract;
use CloakPort\TokenGuard as DefaultGuard;
use CloakPort\Keycloak\TokenGuard as KeycloakGuard;
use CloakPort\Passport\TokenClientGuard as PassportClientGuard;
use CloakPort\Passport\TokenUserGuard as PassportUserGuard;
use KeycloakGuard\Exceptions\InvalidTokenException;
use KeycloakGuard\Exceptions\ResourceAccessNotAllowedException;

enum GuardType implements GuardTypeContract
{
    case KEYCLOAK;
    case PASSPORT_CLIENT;
    case PASSPORT_USER;
    case DEFAULT;

    public static function load(string $backend): GuardTypeContract
    {
        return match(strtolower($backend)) {
            'keycloak' => GuardType::KEYCLOAK,
            'passport_client' => GuardType::PASSPORT_CLIENT,
            'passport_user' => GuardType::PASSPORT_USER,
            default => GuardType::DEFAULT
        };
    }

    /**
     * @throws InvalidTokenException
     * @throws ResourceAccessNotAllowedException
     */
    public function loadFrom(array $config): GuardContract
    {
        return match ($this) {
            self::KEYCLOAK => KeycloakGuard::load($config),
            self::PASSPORT_CLIENT => PassportClientGuard::load($config),
            self::PASSPORT_USER => PassportUserGuard::load($config),
            self::DEFAULT => new DefaultGuard()
        };
    }
}
