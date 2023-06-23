<?php

namespace ClockPort\Tests\Models;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements UserProvider
{
    protected $fillable = [
        'id',
        'username',
    ];

    public function retrieveById($identifier)
    {
    }

    public function retrieveByToken($identifier, $token)
    {
    }

    public function updateRememberToken(\Illuminate\Contracts\Auth\Authenticatable $user, $token)
    {
    }

    public function retrieveByCredentials(array $credentials)
    {
    }

    public function validateCredentials(\Illuminate\Contracts\Auth\Authenticatable $user, array $credentials)
    {
    }
}