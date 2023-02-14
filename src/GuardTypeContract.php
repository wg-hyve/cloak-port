<?php

namespace CloakPort;

use CloakPort\Creator\GuardType;

interface GuardTypeContract
{
    public static function load(string $backend): GuardType;

    public function guardClass(): string;

    public function loadFrom(array $config): GuardContract;
}
