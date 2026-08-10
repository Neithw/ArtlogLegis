<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreFiliacaoPartidariaRequest extends FormRequest
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
            'partido_id' => [
                'required',
                'integer',
                Rule::exists('partidos', 'id')
                    ->whereNull('deleted_at')
            ],

            'data_troca' => [
                'required',
                'date'
            ]
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['partido_id', 'data_troca'])) {
                    return;
                }

                $mandato = $this->route('mandato');

                $filiacaoAtual = $mandato
                    ->ultimaFiliacaoPartidaria()
                    ->first();

                if (! $filiacaoAtual) {
                    $validator->errors()->add(
                        'partido_id',
                        'Este mandato não possui uma filiação partidária registrada.'
                    );

                    return;
                }

                if ((int) $filiacaoAtual->partido_id === $this->integer('partido_id')) {
                    $validator->errors()->add(
                        'partido_id',
                        'O novo partido deve ser diferente do partido atual.'
                    );

                    return;
                }

                $dataTroca = Carbon::parse($this->input('data_troca'));

                if ($dataTroca->lte($filiacaoAtual->data_inicio)) {
                    $validator->errors()->add(
                        'data_troca',
                        'A data da troca deve ser posterior ao início da filiação atual.'
                    );

                    return;
                }

                if ($mandato->data_fim && $dataTroca->gt($mandato->data_fim)) {
                    $validator->errors()->add(
                        'data_troca',
                        'A data da troca não pode ser posterior ao término do mandato.'
                    );

                    return;
                }

                if ($filiacaoAtual->data_fim && $dataTroca->gt($filiacaoAtual->data_fim)) {
                    $validator->errors()->add(
                        'data_troca',
                        'A data da troca não pode ser posterior ao término da filiação atual.'
                    );
                }
            }
        ];
    }
}
