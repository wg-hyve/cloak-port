<?php

namespace CloakPort;

use CloakPort\Creator\GuardType;

interface GuardTypeContract
{
    public static function load(string $backend): GuardTypeContract;

    public function loadFrom(array $config): GuardContract;
}
