<?php

namespace Database\Seeders;

use App\Models\Permissao;
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
                'codigo' => 'camaras:visualizar'
            ],
            [
                'nome' => 'Cadastrar Câmaras',
                'codigo' => 'camaras:criar'
            ],
            [
                'nome' => 'Editar Câmaras',
                'codigo' => 'camaras:editar'
            ],
            [
                'nome' => 'Excluir Câmaras',
                'codigo' => 'camaras:excluir'
            ],
            [
                'nome' => 'Visualizar Usuários',
                'codigo' => 'usuarios:visualizar'
            ],
            [
                'nome' => 'Cadastrar Usuários',
                'codigo' => 'usuarios:criar'
            ],
            [
                'nome' => 'Editar Usuários',
                'codigo' => 'usuarios:editar'
            ],
            [
                'nome' => 'Desativar Usuários',
                'codigo' => 'usuarios:desativar'
            ],
            [
                'nome' => 'Reativar Usuários',
                'codigo' => 'usuarios:reativar'
            ],
            [
                'nome' => 'Excluir Usuários',
                'codigo' => 'usuarios:excluir'
            ],
            [
                'nome' => 'Visualizar Vereadores',
                'codigo' => 'vereadores:visualizar'
            ],
            [
                'nome' => 'Criar Vereadores',
                'codigo' => 'vereadores:criar'
            ],
            [
                'nome' => 'Editar Vereadores',
                'codigo' => 'vereadores:editar'
            ],
            [
                'nome' => 'Excluir Vereadores',
                'codigo' => 'vereadores:excluir'
            ],
            [
                'nome' => 'Visualizar Legislaturas',
                'codigo' => 'legislaturas:visualizar'
            ],
            [
                'nome' => 'Cadastrar Legislaturas',
                'codigo' => 'legislaturas:criar'
            ],
            [
                'nome' => 'Editar Legislaturas',
                'codigo' => 'legislaturas:editar'
            ],
            [
                'nome' => 'Excluir Legislaturas',
                'codigo' => 'legislaturas:excluir'
            ]
        ])->map(function (array $permission): Permissao {
            return Permissao::updateOrCreate(
                [
                    'codigo' => $permission['codigo'],
                ],
                [
                    'nome' => $permission['nome'],
                ]
            );
        });

        $root = Role::updateOrCreate(
            [
                'codigo' => 'root',
            ],
            [
                'nome' => 'Administrador geral',
            ]
        );

        $usuarioComum = Role::updateOrCreate(
            [
                'codigo' => 'usuario_comum',
            ],
            [
                'nome' => 'Usuário Comum',
            ]
        );

        $gerente = Role::updateOrCreate(
            [
                'codigo' => 'gerente'
            ],
            [
                'nome' => 'Gerente'
            ]
        );
    }
}
