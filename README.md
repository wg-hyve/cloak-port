# CloakPort
- [Requirements and limits](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Config](#config)
- [Extending](#extending)

## Requirements

This package proofs instances of Keycloak, Passport Client and Passport User authorizations with JWT.
It needs to track three different authorization mechanics:
- Keycloak
- Passport User
- Passport Client

### Keycloak
https://github.com/wg-hyve/laravel-keycloak-guard/tree/wg


## Passport Client
Regular client credentials grant

### Passport User
Passport User is a client login with a determined user. (password grant)  

## Installation
Require wg-hyve/cloak-port and add GitHub repository in your `composer.json`.

```bash
composer require wg-hyve/cloak-port
```

Publish config if you want to overwrite it. `php artisan vendor:publish --tag=cloak-port-config`

## Usage
Install Passport and Keycloak packages. It should work out of the box.
You still can extend the behavior with your own `GuardType`. Make sure you implement `GuardTypeContract`.
You are able to add your own Guards if you add a new `GuardType`.

### Define CloakPort in auth config & routes
in your `auth.php` file:
```php
<?php

return [
    // ...
    'guards' => [
        'cloak_n_dagger' => [
            'driver' => 'keycloak_passport',
            'provider' => 'users',
        ],
        // ...
    ],
    // ...
];
```

in your `routes/api.php`:
```php
<?php

use App\Http\Controllers\AnyController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:cloak_n_dagger'])->group(function () {
  Route::get('/any-route', [AnyController::class, 'index']);
});
```

## Config

### keycloak_key_identifier
Identify keycloak JWTs if any of the keys `keycloak_key_identifier` section match in your Bearer token payload.

### guards
Loaded guards. The order direction affects the loading order of your guards.
Keycloak and Passport User Guards will always have the highest priority since they are the strictest.

### factory
Replace `GuardType` with your own factory if needed. Keep in mind you still need coverage for `keycloak`, `passport_user` and `passport_client`.

# Extending
## config
``` php
return [
    // ...
    'guards' => [
        'my_new_guard',
        'passport_user',
        'passport_client',
        'keycloak',
        'default'
    ],
    'factory' => My\Package\MyGuardType::class,
];
```

## GuardType

``` php
<?php

namespace My\Package;

use CloakPort\GuardContract;
use CloakPort\GuardTypeContract;
use CloakPort\TokenGuard as DefaultGuard;
use My\Package\TokenGuard as MyGuard;

enum GuardType implements GuardTypeContract
{
    case KEYCLOAK;
    case PASSPORT_CLIENT;
    case PASSPORT_USER;
    case MY_GUARD;
    case DEFAULT;

    public static function load(string $backend): self
    {
        return match(strtolower($backend)) {
            'my_guard' => GuardType::MY_GUARD,
            // ...
            default => GuardType::DEFAULT
        };
    }

    public function loadFrom(array $config): GuardContract
    {
        return match ($this) {
            self::MY_GUARD => MyGuard::load($config),
            // ...
            self::DEFAULT => DefaultGuard::load($config)
        };
    }
}
```

### TokenGuard
Overwrite any other public method like `user` if needed.
Provide a setConfig method and a local $config attribute in your guard.
``` php
<?php

namespace My\Package;

use CloakPort\GuardContract;
use CloakPort\TokenGuard as ParentTokenGuard;
use Illuminate\Contracts\Auth\Guard;

class TokenGuard extends ParentTokenGuard implements Guard, GuardContract
{
    protected array $config = [];
    
    public static function load(array $config): self
    {
        return new self();
    }
    
    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }
    
    public function validate(array $credentials = [])
    {
        // any magic to valid your JWT
        return $this->check();
    }

    public function check()
    {
        return false;
    }
}
```
