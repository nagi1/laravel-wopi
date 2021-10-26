<?php

namespace Nagi\LaravelWopi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WopiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // todo find a way to abstract this
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // todo config this or find clean way to abstract it
        return [];
    }
}
