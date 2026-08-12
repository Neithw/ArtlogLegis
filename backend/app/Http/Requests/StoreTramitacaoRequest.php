<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTramitacaoRequest extends FormRequest
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
        $proposicao = $this->route('proposicao');

        return [
            'unidade_destino_id' => [
                'required',
                'integer',
                Rule::exists('unidades_tramitacao', 'id')
                    ->where(
                        fn($query) => $query
                            ->where('camara_id', $proposicao->camara_id)
                            ->whereNull('deleted_at')
                    )
            ],

            'despacho' => [
                'nullable',
                'string',
                'max:5000'
            ]
        ];
    }
}
