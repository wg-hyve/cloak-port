<?php

use CloakPort\Creator\GuardType;

return [
    'keycloak_key_identifier' => [
        'azp',
        'realm_access',
        'resource_access'
    ],
    'guards' => [
        'keycloak',
        'passport_client',
        'passport_user',
        'default'
    ],
    'factory' => GuardType::class,
];
