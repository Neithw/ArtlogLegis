<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemPautaRequest extends FormRequest
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
        $sessao = $this->route('sessao');

        return [
            'proposicao_id' => [
                'required',
                'integer',

                Rule::exists('proposicoes', 'id')
                    ->where(
                        fn($query) => $query
                            ->where('camara_id', $sessao->camara_id)
                            ->where('legislatura_id', $sessao->legislatura_id)
                            ->where('situacao', 'protocolada')
                            ->whereNull('deleted_at')
                    ),

                Rule::unique('itens_pauta', 'proposicao_id')
                    ->where(
                        fn($query) => $query
                            ->where('sessao_id', $sessao->id)
                    )
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'proposicao_id.required' => 'Selecione uma proposição.',
            'proposicao_id.integer' => 'A proposição selecionada é inválida.',
            'proposicao_id.exists' => 'A proposição selecionada não está disponível para inclusão nesta pauta.',
            'proposicao_id.unique' => 'Esta proposição já está incluída na pauta da sessão.'
        ];
    }
}
