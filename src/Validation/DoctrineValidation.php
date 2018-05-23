<?php

namespace Denngarr\Seat\Fitting\Validation;

use Illuminate\Foundation\Http\FormRequest;

class DoctrineValidation extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'doctrinename' => 'required|string',
            'selectedFits' => 'required|array|min:1'
        ];
    }
}

?>
