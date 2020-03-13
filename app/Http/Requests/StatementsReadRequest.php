<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatementsReadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ticker' => [
                'required',
                'regex:/^[a-z0-9]+\.?[a-z0-9]*:[a-z]{2}$/i',
                'max:256',
                'ends_with:US,CA,MX,UK,AU,NZ'
            ]
        ];
    }
}
