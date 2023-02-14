<?php

namespace CloakPort\Creator;

use CloakPort\Creator\Traits\HasMagicCall;
use CloakPort\GuardContract;

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
