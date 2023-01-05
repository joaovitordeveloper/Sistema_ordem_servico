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
                'nome' => 'Amarela',
                'descricao' => 'Descricao da cor'
            ],

            [
                'nome' => 'Azul',
                'descricao' => 'Descricao da cor'
            ],

            [
                'nome' => 'Roxo',
                'descricao' => 'Descricao da cor'
            ],

            [
                'nome' => 'Vermelho',
                'descricao' => 'Descricao da cor'
            ],

            [
                'nome' => 'Verde',
                'descricao' => 'Descricao da cor'
            ],

            [
                'nome' => 'Lilas',
                'descricao' => 'Descricao da cor'
            ],

            [
                'nome' => 'Amarela',
                'descricao' => 'Descricao da cor'
            ]
        ];

        foreach ($cores as $cor) {
           $corModel->insert($cor); 
        }

        echo "Cores inseridas com sucesso";
    }
}
