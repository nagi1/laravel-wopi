<?php

return [
    'config_repository' =>  Nagi\LaravelWopi\Services\DefaultConfigRepository::class,

    // todo discovery class
    // todo proof validation class
    // todo proof request
    // todo wopi implementation
    // todo document manager

    'server_url' => env('WOPI_SERVER_URL', ''),

    'access_token_ttl' => env('WOPI_ACCESS_TOKEN_TTL', ''),

    'enable_proof_validation' => true,
];
