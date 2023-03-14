<?php

namespace CloakPort;

class Token
{
    public function __construct(public array $header, public array $payload, public string $siganture)
    {}
}
