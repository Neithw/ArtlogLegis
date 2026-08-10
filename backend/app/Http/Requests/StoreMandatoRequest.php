<?php

namespace App\Http\Requests;

use App\Models\Legislatura;
use App\Models\Mandato;
use App\Models\Vereador;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMandatoRequest extends FormRequest
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
            'vereador_id' => [
                'required',
                'integer',
                Rule::exists('vereadores', 'id')
                    ->whereNull('deleted_at')
            ],

            'legislatura_id' => [
                'required',
                'integer',
                Rule::exists('legislaturas', 'id')
                    ->whereNull('deleted_at')
            ],

            'partido_id' => [
                'required',
                'integer',
                Rule::exists('partidos', 'id')
                    ->whereNull('deleted_at')
            ],

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
                if ($validator->errors()->hasAny(['vereador_id', 'legislatura_id', 'partido_id', 'data_inicio', 'data_fim'])) {
                    return;
                }

                $vereador = Vereador::find($this->integer('vereador_id'));
                $legislatura = Legislatura::find($this->integer('legislatura_id'));
                $usuario = $this->user();

                $dataInicio = Carbon::parse($this->input('data_inicio'));

                $dataFim = $this->filled('data_fim')
                    ? Carbon::parse($this->input('data_fim'))
                    : null;

                if (! $usuario->isRoot() && (int) $usuario->camara_id !== (int) $legislatura->camara_id) {
                    $validator->errors()->add(
                        'legislatura_id',
                        'A legislatura selecionada não pertence a sua Câmara.'
                    );

                    return;
                }

                if ((int) $vereador->camara_id !== (int) $legislatura->camara_id) {
                    $validator->errors()->add(
                        'vereador_id',
                        'O vereador deve pertencer à mesma Câmara da legislatura.'
                    );

                    return;
                }

                $camara = $legislatura->camara;

                if (! $camara || ! $camara->ativo) {
                    $validator->errors()->add(
                        'legislatura_id',
                        'Não é possível cadastrar um mandato para uma Câmara inativa.'
                    );

                    return;
                }

                $mandatoExistente = Mandato::withTrashed()
                    ->where('vereador_id', $vereador->id)
                    ->where('legislatura_id', $legislatura->id)
                    ->exists();

                if ($mandatoExistente) {
                    $validator->errors()->add(
                        'vereador_id',
                        'Este vereador já possui um mandato cadastrado para esta legislatura.'
                    );
                }

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
