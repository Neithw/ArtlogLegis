<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class StoreUserRequest extends FormRequest
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
        if (! $this->user()->isRoot()) {
            $this->merge([
                'camara_id' => $this->user()->camara_id
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
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults()
            ],

            'camara_id' => [
                'required',
                'integer',
                Rule::exists('camaras', 'id')
                    ->where('ativo', true)
            ],

            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
                    ->where(fn($query) => $query->where('codigo', '!=', "root"))
            ],

            'permissoes' => [
                'nullable',
                'array'
            ],

            'permissoes.*' => [
                'integer',
                'distinct',
                'exists:permissoes,id'
            ],

            'unidades_tramitacao' => [
                'nullable',
                'array'
            ],

            'unidades_tramitacao.*' => [
                'integer',
                'distinct',
                Rule::exists('unidades_tramitacao', 'id')
                    ->where(
                        fn($query) => $query
                            ->where('camara_id', $this->input('camara_id'))
                            ->whereNull('deleted_at')
                    )
            ]
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $usuarioAutenticado = $this->user();

                if ($usuarioAutenticado->isRoot()) {
                    return;
                }

                if ((int) $this->input('camara_id') !== (int) $usuarioAutenticado->camara_id) {
                    $validator->errors()->add(
                        'camara_id',
                        'Você só pode cadastrar usuários na sua própria Câmara.'
                    );
                }

                $permissoesSolicitadas = collect(
                    $this->input('permissoes', [])
                )->map(fn($permissionId) => (int) $permissionId);

                $permissoesPermitidas = $usuarioAutenticado
                    ->permissoes()
                    ->pluck('permissoes.id')
                    ->map(fn($permissionId) => (int) $permissionId);

                if ($permissoesSolicitadas->diff($permissoesPermitidas)->isNotEmpty()) {
                    $validator->errors()->add(
                        'permissoes',
                        'Você não pode conceder permissões que não possui.'
                    );
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome do usuário.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode possuir mais de 255 caracteres.',

            'email.required' => 'Informe o e-mail do usuário.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.max' => 'O e-mail não pode possuir mais de 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado.',

            'password.required' => 'Informe uma senha inicial.',
            'password.min' => 'A senha deve possuir pelo menos :min caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',

            'camara_id.required' => 'Selecione uma câmara',
            'camara_id.integer' => 'A Câmara selecionada é inválida.',
            'camara_id.exists' => 'A Câmara selecionada não foi encontrada.',

            'role_id.required' => 'Selecione um papel.',
            'role_id.integer' => 'O papel selecionado é inválido.',
            'role_id.exists' => 'O papel selecionado não foi encontrado.',

            'permissoes.array' => 'As permissões devem ser enviadas em uma lista.',
            'permissoes.*.integer' => 'Uma das permissões selecionadas é inválida.',
            'permissoes.*.distinct' => 'Uma permissão foi enviada mais de uma vez.',
            'permissoes.*.exists' => 'Uma das permissões selecionadas não existe.',

            'unidades_tramitacao.array' => 'As unidades de tramitação devem ser enviadas em uma lista.',
            'unidades_tramitacao.*.integer' => 'Uma das unidades de tramitação selecionadas é inválida.',
            'unidades_tramitacao.*.distinct' => 'Uma unidade de tramitação foi selecionada mais de uma vez.',
            'unidades_tramitacao.*.exists' => 'Uma das unidades selecionadas não pertence à Câmara informada ou não está disponível.'
        ];
    }
}
