<?php

namespace App\Database\Seeds;

use App\Models\CorModel;
use CodeIgniter\Database\Seeder;

class CorSeeder extends Seeder
{
    public function run()
    {
        $corModel = new CorModel();

        $cores = [
            [
                'nome' => 'Amarela'
            ],

            [
                'nome' => 'Azul'
            ],

            [
                'nome' => 'Roxo'
            ],

            [
                'nome' => 'Vermelho'
            ],

            [
                'nome' => 'Verde'
            ],

            [
                'nome' => 'Lilas'
            ],

            [
                'nome' => 'Amarela'
            ]
        ];

        foreach ($cores as $cor) {
           $corModel->insert($cor); 
        }

        echo "Cores inseridas com sucesso";
    }
}
