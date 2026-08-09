<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVereadorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vereador = $this->route('vereador');

        return [
            'user_id' => [
                'nullable',
                'integer',

                Rule::exists('users', 'id')
                    ->where(function ($query) use ($vereador) {
                        $query
                            ->where('camara_id', $vereador->camara_id)
                            ->where(function ($query) use ($vereador) {
                                $query->where(function ($query) {
                                    $query
                                        ->where('ativo', true)
                                        ->whereNull('deleted_at');
                                });

                                if ($vereador->user_id !== null) {
                                    $query->orWhere('id', $vereador->user_id);
                                }
                            });
                    }),

                Rule::unique('vereadores', 'user_id')
                    ->ignore($vereador)
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
