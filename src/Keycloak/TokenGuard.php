<?php

namespace CloakPort\Keycloak;

use CloakPort\GuardContract;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use KeycloakGuard\Exceptions\InvalidTokenException;
use KeycloakGuard\Exceptions\ResourceAccessNotAllowedException;
use KeycloakGuard\KeycloakGuard;

class TokenGuard extends KeycloakGuard implements Guard, GuardContract
{
    /**
     * @throws ResourceAccessNotAllowedException
     * @throws InvalidTokenException
     */
    public static function load(array $config): self
    {
        return new self(Auth::createUserProvider($config['provider']), $config['request']);
    }

    public function claims(): array
    {
        return (array) $this->token();
    }

    public function name(): string
    {
        return 'keycloak';
    }
}
