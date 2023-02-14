<?php

namespace CloakPort\Keycloak;

use CloakPort\GuardContract;
use Illuminate\Contracts\Auth\Guard;
use KeycloakGuard\KeycloakGuard;

class TokenGuard extends KeycloakGuard implements Guard, GuardContract
{
    public function name(): string
    {
        return 'keycloak';
    }
}
