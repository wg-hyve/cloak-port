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
        'passport',
        'default'
    ],
    'factory' => GuardType::class,
];
