<?php

namespace CloakPort\Passport;

use CloakPort\GuardContract;
use Illuminate\Contracts\Auth\Guard;
use Laravel\Passport\Guards\TokenGuard as PassportTokenGuard;

class TokenGuard extends PassportTokenGuard implements Guard, GuardContract
{
    public function hasRole(array $resource, string $role): bool
    {
        return true;
    }
    public function scopes(): array
    {
        return [];
    }

    public function hasScope(string|array $scope): bool
    {
        return true;
    }

    public function name(): string
    {
        return 'passport';
    }
}
