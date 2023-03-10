<?php

namespace CloakPort\Passport;

use CloakPort\GuardContract;
use CloakPort\Traits\Decode;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Guards\TokenGuard as PassportTokenGuard;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\PassportUserProvider;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class TokenClientGuard extends PassportTokenGuard implements Guard, GuardContract
{
    use Decode;

    protected mixed $token = null;

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
        $token = null;

        $psr = (new PsrHttpFactory(
            new Psr17Factory,
            new Psr17Factory,
            new Psr17Factory,
            new Psr17Factory
        ))->createRequest(request());

        try {
            $psr = $this->server->validateAuthenticatedRequest($psr);
            $token = $this->tokens->find($psr->getAttribute('oauth_access_token_id'));

        } catch (OAuthServerException $e) {}

        $this->token = $token;

        $this->decode();

        return $this->check();
    }

    public function check()
    {
        return $this->token !== null;
    }

    public function roles($useGlobal = true): array
    {
        return [];
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

    public function claims(): array
    {
        return $this->getClaims();
    }

    public function name(): string
    {
        return 'passport_client';
    }
}
