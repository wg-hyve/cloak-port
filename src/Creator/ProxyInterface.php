<?php

namespace CloakPort\Creator;

use CloakPort\GuardContract;

interface ProxyInterface
{
    public function getGuard(): GuardContract;
}
