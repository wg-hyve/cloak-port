<?php

namespace CloakPort;

interface GuardContract
{
    public function hasRole(array $resource, string $role): bool;

    public function scopes(): array;

    public function hasScope(string|array $scope): bool;

    public function name(): string;
}
