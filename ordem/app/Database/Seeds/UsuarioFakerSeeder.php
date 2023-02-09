<?php

namespace App\Database\Seeds;

use App\Models\UsuarioModel;
use CodeIgniter\Database\Seeder;

class UsuarioFakerSeeder extends Seeder
{
    public function run()
    {
        $usuarioModel = new UsuarioModel();
        $faker = \Faker\Factory::create();

        $criarQuantoUsuarios = 7000;
        $usuariosPush = [];

        for ($i=0; $i < $criarQuantoUsuarios; $i++) { 
            
            \array_push($usuariosPush, [
                'nome' => $faker->unique()->name,
                'email' => $faker->unique()->email,
                'password_hash' => '123456', //alterar quando a hash for apresentada.
                'ativo' => $faker->numberBetween(0,1),//com isso alterna entre true e false
            ]);
        }

        /* echo "<pre>";
        \print_r($usuariosPush);
        exit; */

        $usuarioModel->skipValidation(true)
                     ->protect(false)
                     ->insertBatch($usuariosPush);

        echo "$criarQuantoUsuarios usuarios criados com sucesso";                
    }
}
