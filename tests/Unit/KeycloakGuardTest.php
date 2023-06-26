<?php

namespace ClockPort\Tests\Unit;

use CloakPort\Creator\Exceptions\ProxyMethodNotFound;
use CloakPort\Keycloak\TokenGuard;
use ClockPort\Tests\TestCase;
use ClockPort\Tests\Traits\HasPayload;

class KeycloakGuardTest extends TestCase
{
    use HasPayload;

    public function test_guard_is_keycloak_guard(): void
    {
        $this->assertInstanceOf(TokenGuard::class, $this->getKeycloakGuard()->getGuard());
    }

    public function test_guard_name_is_keycloak(): void
    {
        $this->assertEquals('keycloak', $this->getKeycloakGuard()->name());
    }

    public function test_guard_has_roles(): void
    {
        $this->assertEquals(['client-role-test'], $this->getKeycloakGuard()->roles());
    }

    public function test_guard_has_role(): void
    {
        $this->assertTrue($this->getKeycloakGuard()->hasRole('client-role-test'));
    }

    public function test_guard_misses_role(): void
    {
        $this->assertFalse($this->getKeycloakGuard()->hasRole('nope'));
    }

    public function test_guard_has_scopes(): void
    {
        $this->assertEquals(['read-test', 'write-test'], $this->getKeycloakGuard()->scopes());
    }

    public function test_guard_has_scope(): void
    {
        $this->assertTrue($this->getKeycloakGuard()->hasScope('write-test'));
    }

    public function test_guard_can_validate(): void
    {
        $this->assertTrue($this->getKeycloakGuard()->validate());
    }

    public function test_guard_calls_check_with_true(): void
    {
        $this->assertTrue($this->getKeycloakGuard()->check());
    }

    public function test_guard_exports_claims(): void
    {
        $this->assertEquals((array) $this->getKeycloakGuard()->token(), $this->getKeycloakGuard()->claims());
    }

    public function test_guard_has_no_laravel_user(): void
    {
        $this->assertNull($this->getKeycloakGuard()->user());
    }

    public function test_guard_has_keycloak_user_id(): void
    {
        $this->assertEquals($this->loadJson('jwt.json')['jti'], $this->getKeycloakGuard()->id());
    }

    public function test_guard_misses_proxy_method(): void
    {
        $this->expectException(ProxyMethodNotFound::class);
        $this->expectExceptionMessage('CloakPort\Keycloak\TokenGuard::nope');

        $this->assertNull($this->getKeycloakGuard()->nope());
    }
}
