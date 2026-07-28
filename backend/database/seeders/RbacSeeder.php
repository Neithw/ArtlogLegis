<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = collect([
            [
                'nome' => 'Visualizar Câmaras',
                'codigo' => 'camaras:visualizar',
            ],
            [
                'nome' => 'Cadastrar Câmaras',
                'codigo' => 'camaras:criar',
            ],
            [
                'nome' => 'Editar Câmaras',
                'codigo' => 'camaras:editar',
            ],
            [
                'nome' => 'Excluir Câmaras',
                'codigo' => 'camaras:excluir',
            ],
            [
                'nome' => 'Visualizar Usuários',
                'codigo' => 'usuarios:visualizar',
            ],
            [
                'nome' => 'Cadastrar Usuários',
                'codigo' => 'usuarios:criar',
            ],
            [
                'nome' => 'Editar Usuários',
                'codigo' => 'usuarios:editar',
            ],
            [
                'nome' => 'Desativar Usuários',
                'codigo' => 'usuarios:desativar',
            ],
            [
                'nome' => 'Reativar Usuários',
                'codigo' => 'usuarios:reativar',
            ],
            [
                'nome' => 'Excluir Usuários',
                'codigo' => 'usuarios:excluir',
            ],
        ])->map(function (array $permission): Permission {
            return Permission::updateOrCreate(
                [
                    'codigo' => $permission['codigo'],
                ],
                [
                    'nome' => $permission['nome'],
                ],
            );
        });

        $root = Role::updateOrCreate(
            [
                'codigo' => 'root',
            ],
            [
                'nome' => 'Administrador geral',
            ],
        );

        $usuarioComum = Role::updateOrCreate(
            [
                'codigo' => 'usuario_comum',
            ],
            [
                'nome' => 'Usuário Comum',
            ],
        );

        $root->permissions()->sync(
            $permissions->pluck('id')->all(),
        );

        $usuarioComum->permissions()->sync([]);
    }
}
