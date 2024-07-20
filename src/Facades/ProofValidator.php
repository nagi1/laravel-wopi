<?php

namespace Nagi\LaravelWopi\Facades;

use Illuminate\Support\Facades\Facade;
use Nagi\LaravelWopi\Services\ProofValidator as WopiProofValidator;

/**
 * @method static bool isValid(\Nagi\LaravelWopi\Support\ProofValidatorInput $proofValidatorInput)
 *
 * @see WopiProofValidator
 */
class ProofValidator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WopiProofValidator::class;
    }
}
