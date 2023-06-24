<?php

namespace ClockPort\Tests\Unit;

use CloakPort\Creator\Exceptions\ProxyMethodNotFound;
use CloakPort\Passport\TokenClientGuard;
use ClockPort\Tests\TestCase;
use ClockPort\Tests\Traits\HasPayload;

class PassportClientGuardTest extends TestCase
{
    use HasPayload;

    public function test_guard_is_passport_client_guard(): void
    {
        $this->assertInstanceOf(TokenClientGuard::class, $this->getPassportClientGuard()->getGuard());
    }

    public function test_guard_name_is_passport_client(): void
    {
        $this->assertEquals('passport_client', $this->getPassportClientGuard()->name());
    }

    public function test_guard_has_roles(): void
    {
        $this->assertEquals([], $this->getPassportClientGuard()->roles());
    }

    public function test_guard_misses_role(): void
    {
        $this->assertFalse($this->getPassportClientGuard()->hasRole('nope'));
    }

    public function test_guard_has_scopes(): void
    {
        $this->assertEquals([], $this->getPassportClientGuard()->scopes());
    }

    public function test_guard_can_validate(): void
    {
        $this->assertTrue($this->getPassportClientGuard()->validate());
    }

    public function test_guard_calls_check_with_true(): void
    {
        $this->assertTrue($this->getPassportClientGuard()->check());
    }

    public function test_guard_exports_claims(): void
    {
        $this->assertEquals((array) json_decode($this->load('jwt_passport_client.json')), $this->getPassportClientGuard()->claims());
    }

    public function test_guard_has_no_laravel_user(): void
    {
        $this->assertNull($this->getPassportClientGuard()->user());
    }

    public function test_guard_has_keycloak_user_id(): void
    {
        $this->assertNull($this->getPassportClientGuard()->id());
    }

    public function test_guard_misses_proxy_method(): void
    {
        $this->expectException(ProxyMethodNotFound::class);
        $this->expectExceptionMessage('CloakPort\Passport\TokenClientGuard::nope');

        $this->assertNull($this->getPassportClientGuard()->nope());
    }
}
