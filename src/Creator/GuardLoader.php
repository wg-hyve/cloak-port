<?php

namespace CloakPort\Creator;

use CloakPort\Creator\Traits\HasMagicCall;
use CloakPort\GuardContract;
use CloakPort\GuardTypeContract;

use Exception;
use Illuminate\Http\Request;

class GuardLoader
{
    use HasMagicCall;

    private static array $loaded = [];
    private static ?ProxyGuard $guard = null;
    private static ?Request $request = null;

    public static function load(array $config): ProxyGuard
    {
        self::$request = $config['request'];

        if(self::$guard === null) {
            $guardName = count(array_intersect(config('cloak_n_passport')['keycloak_key_identifier'], array_keys(self::tokenPayload()))) > 0 ? 'keycloak' : 'passport_user';
            $guard = self::getGuard($guardName, $config);

            self::$loaded[] = $guard->name();

            if($guard->validate($config) === false) {
                $guard = self::reload($config);
            }

            self::$guard = new ProxyGuard($guard);
        }

        return self::$guard;
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

                if($guard->validate() === true) {
                    $validGuard = $guard;
                    break;
                }
            }
        }

        return $validGuard;
    }

    private static function tokenPayload(): array
    {
        [$header, $payload, $signature] = explode('.', str_replace('Bearer', '', self::$request->header('Authorization', '..')));

        if($payload) {
            return json_decode(base64_decode($payload), true);
        }

        abort(401, 'No valid Bearer token in Authorization header');
    }

    private static function getGuard($name, $config): GuardContract
    {
        $gardTypeClass = config('cloak_n_passport')['factory'];

        try {
            $guard = $gardTypeClass::load($name)->loadFrom($config);
        }catch(Exception $e) {
            $guard = $gardTypeClass::load('none')->loadFrom($config);
        }

        return $guard;
    }
}
