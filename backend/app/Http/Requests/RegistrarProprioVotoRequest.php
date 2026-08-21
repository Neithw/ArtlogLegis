<?php

namespace App\Http\Requests;

use App\Models\Voto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarProprioVotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'escolha' => [
                'required',
                'string',
                Rule::in(array_keys(Voto::ESCOLHAS)),
            ],
        ];
    }
}
