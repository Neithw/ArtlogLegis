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
        return $this->user()?->can('usuarios:criar') ?? false;
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
                'exists:camaras,id'
            ],

            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
                    ->where(fn($query) => $query->where('codigo', '!=', "root"))
            ],

            'ativo' => [
                'required',
                'boolean',
            ],

            'permissions.*' => [
                'integer',
                'distinct',
                'exists:permissions,id'
            ]
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $usuarioAutenticado = $this->user();

                if ($usuarioAutenticado->hasRole('root')) {
                    return;
                }

                if ((int) $this->input('camara_id') !== (int) $usuarioAutenticado->camara_id) {
                    $validator->errors()->add(
                        'camara_id',
                        'Você só pode cadastrar usuários na sua própria Câmara.'
                    );
                }

                $permissionsSolicitadas = collect(
                    $this->input('permissions', [])
                )->map(fn($permissionId) => (int) $permissionId);

                $permissionsPermitidas = $usuarioAutenticado
                    ->permissions()
                    ->pluck('permissions.id')
                    ->map(fn($permissionId) => (int) $permissionId);

                if ($permissionsSolicitadas->diff($permissionsPermitidas)->isNotEmpty()) {
                    $validator->errors()->add(
                        'permissions',
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
            'password.confirmed' => 'A confirmação da senha não corresponde.',

            'camara_id.required' => 'Selecione uma câmara',
            'camara_id.integer' => 'A Câmara selecionada é inválida.',
            'camara_id.exists' => 'A Câmara selecionada não foi encontrada.',

            'role_id.required' => 'Selecione um papel.',
            'role_id.integer' => 'O papel selecionado é inválido.',
            'role_id.exists' => 'O papel selecionado não foi encontrado.',

            'ativo.required' => 'Informe o status do usuário.',
            'ativo.boolean' => 'O status informado é inválido.',

            'permissions.array' => 'As permissões devem ser enviadas em uma lista.',
            'permissions.*.integer' => 'Uma das permissões selecionadas é inválida.',
            'permissions.*.distinct' => 'Uma permissão foi enviada mais de uma vez.',
            'permissions.*.exists' => 'Uma das permissões selecionadas não existe.',
        ];
    }
}
