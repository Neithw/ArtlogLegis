<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $usuarioAutenticado = $this->user();
        $user = $this->route('user');

        if (! $usuarioAutenticado || ! $user instanceof User || ! $usuarioAutenticado->can('usuarios:editar')) {
            return false;
        }

        if ($user->isRoot()) {
            return false;
        }

        if (! $usuarioAutenticado->isRoot() && (int) $user->camara_id !== (int) $usuarioAutenticado->camara_id) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

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
                Rule::unique('users', 'email')->ignore($user)
            ],

            'password' => [
                'nullable',
                'confirmed',
                Password::defaults()
            ],

            'camara_id' => [
                'required',
                'integer',
                Rule::exists('camaras', 'id')
                    ->where(function ($query) {
                        $query
                            ->where('ativo', true)
                            ->whereNull('deleted_at');
                    })
            ],

            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
                    ->where(function ($query) {
                        $query
                            ->where('codigo', '!=', 'root');
                    })
            ],

            'permissoes' => [
                'nullable',
                'array'
            ],

            'permissoes.*' => [
                'integer',
                'distinct',
                'exists:permissoes,id'
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
                        'Você só pode vincular usuários à sua própria Câmara.'
                    );
                }

                $permissoesSolicitadas = collect(
                    $this->input('permissoes', [])
                )->map(fn($permissionId): int => (int) $permissionId);

                $permissoesPermitidas = $usuarioAutenticado
                    ->permissoes()
                    ->pluck('permissoes.id')
                    ->map(fn($permissionId): int => (int) $permissionId);

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

            'password.confirmed' => 'A confirmação da senha não corresponde.',

            'camara_id.required' => 'Selecione uma Câmara.',
            'camara_id.integer' => 'A Câmara selecionada é inválida.',
            'camara_id.exists' => 'A Câmara selecionada não está disponível.',

            'role_id.required' => 'Selecione um papel.',
            'role_id.integer' => 'O papel selecionado é inválido.',
            'role_id.exists' => 'O papel selecionado não está disponível.',

            'permissoes.array' => 'As permissões devem ser enviadas em uma lista.',
            'permissoes.*.integer' => 'Uma das permissões selecionadas é inválida.',
            'permissoes.*.distinct' => 'Uma permissão foi enviada mais de uma vez.',
            'permissoes.*.exists' => 'Uma das permissões selecionadas não existe.',
        ];
    }
}
