<?php

namespace App\Database\Seeds;

use App\Models\GrupoModel;
use CodeIgniter\Database\Seeder;

class GrupoTempSeeder extends Seeder
{
    public function run()
    {
        $grupoModel = new GrupoModel();

        $grupos = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Grupo com acesso total ao sistema', 
                'exibir' => false
            ], 
            [
                'nome' => 'Clientes',
                'descricao' => 'Grupo para acesso dos clientes para ver seus pedidos', 
                'exibir' => false
            ], 
            [
                'nome' => 'Atendentes',
                'descricao' => 'Grupo com acesso ao sistema para realizar atendimento aos clientes', 
                'exibir' => false
            ]
        ];

        foreach ($grupos as $grupo) {
            $grupoModel->insert($grupo);
        }

        echo "Grupos criados com sucesso." . PHP_EOL;
    }
}
