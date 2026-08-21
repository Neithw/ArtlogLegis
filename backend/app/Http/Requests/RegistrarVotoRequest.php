<?php

namespace App\Http\Requests;

use App\Models\Voto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarVotoRequest extends FormRequest
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
        $votacao = $this->route('votacao');

        $sessaoId = $votacao->itemPauta->sessao_id;

        return [
            'mandato_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('sessao_presencas', 'mandato_id')
                    ->where(
                        fn($query) => $query
                            ->where('sessao_id', $sessaoId)
                            ->where('situacao', 'presente')
                    )
            ],

            'escolha' => [
                'required',
                Rule::in(array_keys(Voto::ESCOLHAS))
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'mandato_id.required' => 'O mandato é obrigatório.',
            'mandato_id.integer' => 'O mandato informado é inválido.',
            'mandato_id.exists' => 'O mandato informado não possui presença válida nesta sessão.',

            'escolha.required' => 'O voto é obrigatório.',
            'escolha.in' => 'O voto selecionado é inválido.',
        ];
    }
}
