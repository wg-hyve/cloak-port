<?php

namespace CloakPort\Passport;

use CloakPort\GuardContract;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Guards\TokenGuard as PassportTokenGuard;
use Laravel\Passport\PassportUserProvider;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;

class TokenUserGuard extends PassportTokenGuard implements Guard, GuardContract
{
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

    public function hasRole(array $resource, string $role): bool
    {
        return true;
    }
    public function scopes(): array
    {
        return [];
    }

    public function hasScope(string|array $scope): bool
    {
        return true;
    }

    public function name(): string
    {
        return 'passport_user';
    }
}
