<?php

namespace ClockPort\Tests\Unit;

use CloakPort\Creator\ProxyGuard;
use ClockPort\Tests\TestCase;
use ClockPort\Tests\Traits\HasPayload;

class GuardLoaderTest extends TestCase
{
    use HasPayload;

    public function test_guard_loader_initialises_proxy_guard(): void
    {
        $this->assertInstanceOf(ProxyGuard::class, $this->getKeycloakGuard());
    }
}
