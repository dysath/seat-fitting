<?php

namespace Denngarr\Seat\Fitting\Validation;

use Illuminate\Foundation\Http\FormRequest;

class DoctrineValidation extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctrinename' => 'required|string',
            'selectedFits' => 'required|array|min:1'
        ];
    }
}

?>
