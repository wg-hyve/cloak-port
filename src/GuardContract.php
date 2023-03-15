<?php

namespace CloakPort;

interface GuardContract
{
    public static function load(array $config): self;

    public function roles(bool $useGlobal = true): array;

    public function hasRole(array|string $roles): bool;

    public function scopes(): array;

    public function hasScope(string|array $scope): bool;

    public function claims(): array;

    public function name(): string;
}
