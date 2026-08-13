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
        // Be carefull with database based config!
        $config = app(ConfigRepositoryInterface::class);
        $isProofValidationEnabled = $config->getEnableProofValidation();

        if (! $isProofValidationEnabled) {
            return $next($request);
        }

        $proofValidatorInput = ProofValidatorInput::fromRequest($request);

        if ($config->isMicrosoft365Enabled() && $proofValidatorInput->accessToken !== null) {
            $proofValidatorInput->accessToken = urldecode($proofValidatorInput->accessToken);
        }

        if (ProofValidator::isValid($proofValidatorInput)) {
            return $next($request);
        }

        return abort(500);
    }
}
