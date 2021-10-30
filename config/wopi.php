<?php

return [
    'config_repository' => Nagi\LaravelWopi\Services\DefaultConfigRepository::class,

    'wopi_implementation' => Nagi\LaravelWopi\LaravelWopi::class,

    'wopi_request' =>  Nagi\LaravelWopi\Http\Requests\WopiRequest::class,

    // Todo implement example storage manager
    'document_manager' =>  null,

    'server_url' => env('WOPI_SERVER_URL', ''),

    'access_token_ttl' => env('WOPI_ACCESS_TOKEN_TTL', 0),

    'enable_proof_validation' => true,

    'support_delete' => false,

    'support_rename' => false,

    'support_update' => true,

    'support_locks' => false,

];
