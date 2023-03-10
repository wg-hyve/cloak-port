<?php

namespace CloakPort;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;

class TokenGuard implements Guard, GuardContract
{
    public static function load(array $config): self
    {
        return new self();
    }

    public function user()
    {
        return null;
    }

    public function validate(array $credentials = [])
    {
        return false;
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

    public function roles($useGlobal = true): array
    {
        return [];
    }

    public function hasRole(array $resource, string $role): bool
    {
        return true;
    }

    public function scopes(): array
    {
        return [];
    }

    public function claims(): array
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
