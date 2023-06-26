<?php

namespace ClockPort\Tests\Unit;

use CloakPort\Creator\Exceptions\ProxyMethodNotFound;
use CloakPort\Passport\TokenUserGuard;
use ClockPort\Tests\Models\User;
use ClockPort\Tests\TestCase;
use ClockPort\Tests\Traits\HasPayload;

class PassportUserGuardTest extends TestCase
{
    use HasPayload;

    public function test_guard_is_passport_user_guard(): void
    {
        $this->assertInstanceOf(TokenUserGuard::class, $this->getPassportUserGuard()->getGuard());
    }

    public function test_guard_name_is_passport_user(): void
    {
        $this->assertEquals('passport_user', $this->getPassportUserGuard()->name());
    }

    public function test_guard_has_roles(): void
    {
        $this->assertEquals([], $this->getPassportUserGuard()->roles());
    }

    public function test_guard_misses_role(): void
    {
        $this->assertFalse($this->getPassportUserGuard()->hasRole('nope'));
    }

    public function test_guard_has_scopes(): void
    {
        $this->assertEquals([], $this->getPassportUserGuard()->scopes());
    }

    public function test_guard_has_no_scope(): void
    {
        $this->assertFalse($this->getPassportUserGuard()->hasScope('nope'));
    }

    public function test_guard_can_validate(): void
    {
        $this->assertTrue($this->getPassportUserGuard()->validate());
    }

    public function test_guard_calls_check_with_true(): void
    {
        $this->assertTrue($this->getPassportUserGuard()->check());
    }

    public function test_guard_exports_claims(): void
    {
        $this->assertEquals((array) json_decode($this->load('jwt_passport_user.json')), $this->getPassportUserGuard()->claims());
    }

    public function test_guard_has_laravel_user(): void
    {
        $user = $this->getPassportUserGuard()->user();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->username);
    }

    public function test_guard_has_keycloak_user_id(): void
    {
        $this->assertEquals(1, $this->getPassportUserGuard()->id());
    }

    public function test_guard_misses_proxy_method(): void
    {
        $this->expectException(ProxyMethodNotFound::class);
        $this->expectExceptionMessage('CloakPort\Passport\TokenUserGuard::nope');

        $this->assertNull($this->getPassportUserGuard()->nope());
    }
}
