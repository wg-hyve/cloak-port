<?php

namespace ClockPort\Tests\Unit;

use CloakPort\Creator\Exceptions\ProxyMethodNotFound;
use CloakPort\TokenGuard;
use ClockPort\Tests\TestCase;
use ClockPort\Tests\Traits\HasPayload;

class TokenTest extends TestCase
{
    use HasPayload;

    public function test_guard_is_passport_user_guard(): void
    {
        $this->assertInstanceOf(TokenGuard::class, $this->getDefaultGuard()->getGuard());
    }

    public function test_guard_name_is_passport_user(): void
    {
        $this->assertEquals('default', $this->getDefaultGuard()->name());
    }

    public function test_guard_has_roles(): void
    {
        $this->assertEquals([], $this->getDefaultGuard()->roles());
    }

    public function test_guard_misses_role(): void
    {
        $this->assertFalse($this->getDefaultGuard()->hasRole('nope'));
    }

    public function test_guard_has_scopes(): void
    {
        $this->assertEquals([], $this->getDefaultGuard()->scopes());
    }

    public function test_guard_can_not_validate(): void
    {
        $this->assertFalse($this->getDefaultGuard()->validate());
    }

    public function test_guard_calls_check_with_false(): void
    {
        $this->assertFalse($this->getDefaultGuard()->check());
    }

    public function test_guard_exports_claims(): void
    {
        $this->assertEquals([], $this->getDefaultGuard()->claims());
    }

    public function test_guard_has_no_user(): void
    {
        $this->assertNull($this->getDefaultGuard()->user());
        $this->assertFalse($this->getDefaultGuard()->hasUser());
    }

    public function test_guard_has_no_user_id(): void
    {
        $this->assertNull($this->getDefaultGuard()->id());
    }

    public function test_guard_misses_proxy_method(): void
    {
        $this->expectException(ProxyMethodNotFound::class);
        $this->expectExceptionMessage('CloakPort\TokenGuard::nope');

        $this->assertNull($this->getDefaultGuard()->nope());
    }

    public function test_guard_has_no_client(): void
    {
        $this->assertNull($this->getDefaultGuard()->client());
    }

    public function test_guard_has_no_guest(): void
    {
        $this->assertFalse($this->getDefaultGuard()->guest());
    }
}
