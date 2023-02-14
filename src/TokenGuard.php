<?php

namespace CloakPort;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;

class TokenGuard implements Guard, GuardContract
{
    public function user()
    {
        return null;
    }

    public function validate(array $credentials = [])
    {
        return true;
    }

    public function id()
    {
        return null;
    }

    public function check()
    {
        return false;
    }

    public function client()
    {
        return null;
    }

    public function guest(): bool
    {
        return false;
    }

    public function setUser(Authenticatable $user): self
    {
        return $this;
    }

    public function hasUser(): bool
    {
        return false;
    }

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
        return 'default';
    }
}
