<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateMandatoRequest extends FormRequest
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
            'data_inicio' => [
                'required',
                'date'
            ],

            'data_fim' => [
                'nullable',
                'date',
                'after_or_equal:data_inicio'
            ]
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['data_inicio', 'data_fim'])) {
                    return;
                }

                $mandato = $this->route('mandato');
                $legislatura = $mandato->legislatura;

                $dataInicio = Carbon::parse($this->input('data_inicio'));

                $dataFim = $this->filled('data_fim')
                    ? Carbon::parse($this->input('data_fim'))
                    : null;

                if ($dataInicio->lt($legislatura->data_inicio)) {
                    $validator->errors()->add(
                        'data_inicio',
                        'A data de início do mandato não pode ser anterior ao início da legislatura.'
                    );
                }

                if ($dataInicio->gt($legislatura->data_fim)) {
                    $validator->errors()->add(
                        'data_inicio',
                        'A data de início do mandato não pode ser posterior ao fim da legislatura.'
                    );
                }

                if ($dataFim && $dataFim->gt($legislatura->data_fim)) {
                    $validator->errors()->add(
                        'data_fim',
                        'A data de término do mandato não pode ser posterior ao fim da legislatura.'
                    );
                }
            }
        ];
    }
}
