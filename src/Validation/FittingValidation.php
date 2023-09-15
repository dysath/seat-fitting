<?php

namespace Denngarr\Seat\Fitting\Validation;

use Illuminate\Foundation\Http\FormRequest;

class FittingValidation extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fitSelection' => 'nullable',
            'eftfitting' => 'required'
        ];
    }
}
