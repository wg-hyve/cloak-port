<?php

namespace CloakPort\Creator;

use CloakPort\Creator\Traits\HasMagicCall;
use CloakPort\GuardContract;
use CloakPort\GuardTypeContract;

class GuardLoader
{
    use HasMagicCall;

    private static array $loaded = [];
    public static function load(array $config): ProxyGuard
    {
        $guardName = count(array_intersect(config('cloak_n_passport')['keycloak_key_identifier'], array_keys(self::tokenPayload()))) > 0 ? 'keycloak' : 'passport_user';
        $guard = GuardType::load($guardName)->loadFrom($config);

        self::$loaded[] = $guard->name();

        if($guard->validate(['request' => request()]) === false) {
            $guard = self::reload($config);
        }

        return new ProxyGuard($guard);
    }

    public static function reload(array $config): ?GuardContract
    {
        $validGuard = null;
        $gardTypeClass = config('cloak_n_passport')['factory'];

        foreach (config('cloak_n_passport')['guards'] as $guardType) {

            if(in_array($guardType, self::$loaded) === false) {
                $guardTypeInstance = $gardTypeClass::load($guardType);

                assert($guardTypeInstance instanceof GuardTypeContract);

                $guard = $guardTypeInstance->loadFrom($config);

                self::$loaded[] = $guard->name();

                if($guard->validate()) {
                    $validGuard = $guard;
                    break;
                }
            }
        }

        return $validGuard;
    }

    private static function tokenPayload(): array
    {
        [$header, $payload, $signature] = explode('.', str_replace('Bearer', '', request()->header('Authorization', '..')));

        if($payload) {
            return json_decode(base64_decode($payload), true);
        }
        
        abort(401, 'No valid Bearer token in Authorization header');
    }
}
