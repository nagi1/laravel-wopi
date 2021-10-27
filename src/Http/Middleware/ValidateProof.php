<?php

namespace Nagi\LaravelWopi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Nagi\LaravelWopi\Facades\ProofValidator;
use Nagi\LaravelWopi\Support\ProofValidatorInput;

class ValidateProof
{
    public function handle(Request $request, Closure $next)
    {
        if (! ProofValidator::isValid(ProofValidatorInput::fromRequest($request))) {
            // todo check response in docs
            return abort(500);
        }

        return $next($request);
    }
}
