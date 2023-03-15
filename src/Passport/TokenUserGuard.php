<?php

namespace CloakPort\Passport;

use CloakPort\GuardContract;
use CloakPort\Traits\Decode;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Guards\TokenGuard as PassportTokenGuard;
use Laravel\Passport\PassportUserProvider;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;

class TokenUserGuard extends PassportTokenGuard implements Guard, GuardContract
{
    use Decode;

    public static function load(array $config): self
    {
        return new self(
            app()->make(ResourceServer::class),
            new PassportUserProvider(Auth::createUserProvider($config['provider']), $config['provider']),
            app()->make(TokenRepository::class),
            app()->make(ClientRepository::class),
            app()->make('encrypter'),
            app()->make('request')
        );
    }
    public function validate(array $credentials = [])
    {
        $this->decode();

        return ! is_null((new static(
            $this->server,
            $this->provider,
            $this->tokens,
            $this->clients,
            $this->encrypter,
            $credentials['request'],
        ))->user());
    }

    public function roles(bool $useGlobal = true): array
    {
        return [];
    }

    public function hasRole(array|string $roles): bool
    {
        return false;
    }
    public function scopes(): array
    {
        return [];
    }

    public function hasScope(string|array $scope): bool
    {
        return false;
    }

    public function claims(): array
    {
        return $this->getClaims();
    }

    public function name(): string
    {
        return 'passport_user';
    }
}
