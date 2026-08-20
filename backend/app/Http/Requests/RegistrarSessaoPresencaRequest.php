<?php

namespace App\Http\Requests;

use App\Models\SessaoPresenca;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RegistrarSessaoPresencaRequest extends FormRequest
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
        return [
            'situacao' => [
                'required',
                'string',
                Rule::in(array_keys(SessaoPresenca::SITUACOES))
            ],

            'observacao' => [
                'required_if:situacao,justificada',
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $sessao = $this->route('sessao');
                $mandato = $this->route('mandato');

                if ((int) $mandato->legislatura_id !== (int) $sessao->legislatura_id) {
                    $validator->errors()->add(
                        'mandato',
                        'O mandato selecionado não pertence à legislatura da sessão.'
                    );

                    return;
                }

                if (! $mandato->estaVigenteEm($sessao->data_hora_inicio_previsto)) {
                    $validator->errors()->add(
                        'mandato',
                        'O mandato não estava vigente na data prevista da sessão.'
                    );
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'situacao.required' => 'Selecione a situação da presença.',
            'situacao.string' => 'A situação da presença deve ser um texto válido.',
            'situacao.in' => 'A situação da presença selecionada é inválida.',

            'observacao.required_if' => 'Informe a justificativa da ausência.',
            'observacao.string' => 'A observação deve ser um texto válido.',
            'observacao.max' => 'A observação não pode ter mais de 1000 caracteres.',
        ];
    }
}
