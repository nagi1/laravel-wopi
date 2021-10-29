<?php

namespace Nagi\LaravelWopi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use Nagi\LaravelWopi\Facades\ProofValidator;
use Nagi\LaravelWopi\Support\ProofValidatorInput;

class ValidateProof
{
    public function handle(Request $request, Closure $next)
    {
        $isproofValidationEnabled = app(ConfigRepositoryInterface::class)->getEnableProofValidation();

        if ($isproofValidationEnabled && ProofValidator::isValid(ProofValidatorInput::fromRequest($request))) {
            return $next($request);
        }

        // todo check response in docs
        return abort(500);
    }
}
