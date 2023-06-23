<?php

namespace CloakPort\Traits;

use CloakPort\Token;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Arr;

trait Decode
{

    protected ?Token $loadedToken = null;
    protected ?array $decodedToken = null;

    public function bearer(): string
    {
        return str_replace(
            'Bearer ',
            '',
            request()->header('authorization') ?: Arr::get(getallheaders(), 'Authorization') ?? $this->config['request']->header('authorization')
        );
    }

    public function decode(): ?array
    {
        $tks = explode('.', $this->bearer());
        [$headb64, $bodyb64, $sigb64] = $tks;

        $this->loadedToken = new Token(
            json_decode(JWT::urlsafeB64Decode($headb64), true, 512, JSON_BIGINT_AS_STRING),
            json_decode(JWT::urlsafeB64Decode($bodyb64), true, 512, JSON_BIGINT_AS_STRING),
            JWT::urlsafeB64Decode($sigb64)
        );

        $this->decodedToken = $this->loadedToken->payload;

        return $this->decodedToken;
    }

    public function getClaims(): array
    {
        return $this->decodedToken;
    }
}
