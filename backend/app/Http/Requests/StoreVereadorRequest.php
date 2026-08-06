<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVereadorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if (! $user->isRoot()) {
            $this->merge([
                'camara_id' => $user->camara_id
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'nullable',
                'integer',

                Rule::exists('users', 'id')
                    ->where(function ($query) {
                        $query
                            ->where('camara_id', $this->input('camara_id'))
                            ->where('ativo', true)
                            ->whereNull('deleted_at');
                    }),

                Rule::unique('vereadores', 'user_id')
            ],

            'camara_id' => [
                'required',
                'integer',

                Rule::exists('camaras', 'id')
                    ->where(function ($query) {
                        $query
                            ->where('ativo', true)
                            ->whereNull('deleted_at');
                    })
            ],

            'nome' => [
                'required',
                'string',
                'max:255'
            ],

            'nome_parlamentar' => [
                'nullable',
                'string',
                'max:255'
            ],

            'email_institucional' => [
                'nullable',
                'email',
                'max:255'
            ],

            'telefone_institucional' => [
                'nullable',
                'string',
                'max:255'
            ],

            'biografia' => [
                'nullable',
                'string'
            ],
        ];
    }
}
