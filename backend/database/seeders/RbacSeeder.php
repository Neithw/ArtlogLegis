<?php

namespace Database\Seeders;

use App\Models\Permissao;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            [
                'nome' => 'Visualizar Câmaras',
                'codigo' => 'camaras:visualizar'
            ],
            [
                'nome' => 'Editar Câmaras',
                'codigo' => 'camaras:editar'
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
            ],
            [
                'nome' => 'Visualizar Partidos',
                'codigo' => 'partidos:visualizar'
            ],
            [
                'nome' => 'Visualizar Mandatos',
                'codigo' => 'mandatos:visualizar'
            ],
            [
                'nome' => 'Cadastrar Mandatos',
                'codigo' => 'mandatos:criar'
            ],
            [
                'nome' => 'Editar Mandatos',
                'codigo' => 'mandatos:editar'
            ],
            [
                'nome' => 'Excluir Mandatos',
                'codigo' => 'mandatos:excluir'
            ],
            [
                'nome' => 'Restaurar Mandatos',
                'codigo' => 'mandatos:restaurar'
            ],
            [
                'nome' => 'Visualizar Tipos de Proposição',
                'codigo' => 'tipos-proposicao:visualizar'
            ],
            [
                'nome' => 'Cadastrar Tipos de Proposição',
                'codigo' => 'tipos-proposicao:criar'
            ],
            [
                'nome' => 'Editar Tipos de Proposição',
                'codigo' => 'tipos-proposicao:editar'
            ],
            [
                'nome' => 'Excluir Tipos de Proposição',
                'codigo' => 'tipos-proposicao:excluir'
            ],
            [
                'nome' => 'Restaurar Tipos de Proposição',
                'codigo' => 'tipos-proposicao:restaurar'
            ],
            [
                'nome' => 'Visualizar Proposições',
                'codigo' => 'proposicoes:visualizar'
            ],
            [
                'nome' => 'Cadastrar Proposições',
                'codigo' => 'proposicoes:criar'
            ],
            [
                'nome' => 'Editar Proposições',
                'codigo' => 'proposicoes:editar'
            ],
            [
                'nome' => 'Excluir Proposições',
                'codigo' => 'proposicoes:excluir'
            ],
            [
                'nome' => 'Restaurar Proposições',
                'codigo' => 'proposicoes:restaurar'
            ],
        ])->each(function (array $permission): void {
            Permissao::updateOrCreate(
                [
                    'codigo' => $permission['codigo'],
                ],
                [
                    'nome' => $permission['nome'],
                ]
            );
        });

        Role::updateOrCreate(
            [
                'codigo' => 'root',
            ],
            [
                'nome' => 'Administrador geral',
            ]
        );

        Role::updateOrCreate(
            [
                'codigo' => 'usuario_comum',
            ],
            [
                'nome' => 'Usuário Comum',
            ]
        );

        Role::updateOrCreate(
            [
                'codigo' => 'gerente'
            ],
            [
                'nome' => 'Gerente'
            ]
        );
    }
}
