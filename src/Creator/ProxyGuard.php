<?php

namespace CloakPort\Creator;

use CloakPort\Creator\Traits\HasMagicCall;
use CloakPort\GuardContract;

/**
 * @method null user()
 * @method bool validate(array $credentials = [])
 * @method mixed id()
 * @method bool check()
 * @method bool hasUser()
 * @method array roles(bool $useGlobal = true)
 * @method bool hasRole(array|string $roles)
 * @method array scopes()
 * @method array claims()
 * @method bool hasScope(string|array $scope)
 * @method string name()
 */
final class ProxyGuard implements ProxyInterface
{
    use HasMagicCall;

    public function __construct(private GuardContract $guard)
    {
    }

    public function getGuard(): GuardContract
    {
        return $this->guard;
    }
}
