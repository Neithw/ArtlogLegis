<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RootUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rootRole = Role::query()
            ->where('codigo', 'root')
            ->firstOrFail();

        $rootUser = User::firstOrCreate(
            [
                'email' => 'root@sistema.test',
            ],
            [
                'name' => 'Administrador Geral',
                'password' => 'password',
                'camara_id' => null,
                'role_id' => $rootRole->id,
                'ativo' => true,
            ],
        );

        $rootUser->update([
            'camara_id' => null,
            'role_id' => $rootRole->id,
            'ativo' => true,
        ]);
    }
}
