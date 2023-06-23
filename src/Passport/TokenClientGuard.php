<?php

namespace CloakPort\Passport;

use CloakPort\GuardContract;
use CloakPort\Traits\Decode;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Container\BindingResolutionException;
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
    protected array $config = [];

    /**
     * @throws BindingResolutionException
     */
    public static function load(array $config): self
    {
        $guard = new self(
            app()->make(ResourceServer::class),
            new PassportUserProvider(Auth::createUserProvider($config['provider']), $config['provider']),
            app()->make(TokenRepository::class),
            app()->make(ClientRepository::class),
            app()->make('encrypter'),
            app()->make('request')
        );

        $guard->setConfig($config);

        return $guard;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function validate(array $credentials = [])
    {
        $token = null;

        $psr = (new PsrHttpFactory(
            new Psr17Factory,
            new Psr17Factory,
            new Psr17Factory,
            new Psr17Factory
        ))->createRequest($this->config['request']);

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
        return 'passport_client';
    }
}
