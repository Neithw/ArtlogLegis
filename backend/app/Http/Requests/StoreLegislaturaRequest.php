<?php

namespace App\Http\Requests;

use App\Models\Legislatura;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreLegislaturaRequest extends FormRequest
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
        $usuario = $this->user();

        if ($usuario && ! $usuario->isRoot()) {
            $this->merge([
                'camara_id' => $usuario->camara_id
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
            'camara_id' => [
                'required',
                'integer',
                Rule::exists('camaras', 'id')
                    ->where('ativo', true)
            ],

            'numero' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
                Rule::unique('legislaturas', 'numero')
                    ->where(fn($query) => $query->where('camara_id', $this->integer('camara_id')))
            ],

            'data_inicio' => [
                'required',
                'date'
            ],

            'data_fim' => [
                'required',
                'date',
                'after:data_inicio'
            ]
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $existeSobreposicao = Legislatura::query()
                    ->where('camara_id', $this->integer('camara_id'))
                    ->whereDate('data_inicio', '<=', $this->input('data_fim'))
                    ->whereDate('data_fim', '>=', $this->input('data_inicio'))
                    ->exists();

                if ($existeSobreposicao) {
                    $validator->errors()->add(
                        'data_inicio',
                        'O período informado se sobrepõe ao de outra legislatura desta Câmara.'
                    );
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'camara_id.required' => 'A Câmara é obrigatória.',
            'camara_id.integer' => 'A Câmara informada é inválida.',
            'camara_id.exists' => 'A Câmara informada não foi encontrada.',

            'numero.required' => 'O número da legislatura é obrigatório.',
            'numero.integer' => 'O número da legislatura deve ser um número inteiro.',
            'numero.min' => 'O número da legislatura deve ser maior que zero.',
            'numero.max' => 'O número da legislatura é inválido.',
            'numero.unique' => 'Já existe uma legislatura com esse número nesta Câmara.',

            'data_inicio.required' => 'A data de início é obrigatória.',
            'data_inicio.date' => 'A data de início informada é inválida.',

            'data_fim.required' => 'A data de término é obrigatória.',
            'data_fim.date' => 'A data de término informada é inválida.',
            'data_fim.after' => 'A data de término deve ser posterior à data de início.',
        ];
    }
}
