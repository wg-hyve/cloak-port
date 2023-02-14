<?php

use CloakPort\Creator\GuardType;

return [
    'keycloak_key_identifier' => [
        'azp',
        'realm_access',
        'resource_access'
    ],
    'guards' => [
        'passport_user',
        'passport_client',
        'keycloak',
        'default'
    ],
    'factory' => GuardType::class,
];
