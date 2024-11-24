<?php

namespace App\Database\Seeds;

use App\Models\UsuarioModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;

class UsuarioFakerSeeder extends Seeder
{
    public function run()
    {
        $usuarioModel = new UsuarioModel();

        $faker = Factory::create();

        $criarQuantosUsuarios = 50;

        $usuariosPush = [];

        for ($i = 0; $i < $criarQuantosUsuarios; $i++) {
            array_push($usuariosPush, [
                'nome' => $faker->unique()->name,
                'email' => $faker->unique()->email(),
                'password_hash' => '123456', 
                'ativo' => true
            ]);
        }

        $usuarioModel
            ->skipValidation(true)
            ->protect(false) // bypass protect in allowedFields
            ->insertBatch($usuariosPush);
     
        echo "$criarQuantosUsuarios semeados com sucesso!";
    }
}
