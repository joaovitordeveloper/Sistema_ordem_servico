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
            [//ID 1 do grupo administrador.
                'nome' => 'Administrador',
                'descricao' => 'Grupo com acesso total ao sistema',
                'exibir' => false
            ],
            [//ID 2 do frupo de clientes. 
                'nome' => 'Clientes',
                'descricao' => 'Este grupo e destinado a atribuição de cliente, pois os mesmos poderão logar no sistema para visualisar suas ordems de serviço.',
                'exibir' => false
            ],
            [//ID 3 do frupo de atendentes.
                'nome' => 'Atendentes',
                'descricao' => 'Esse grupo acessa o sistema para atender os clientes',
                'exibir' => false
            ],
        ];

        foreach ($grupos as $grupo) {
            $grupoModel->insert($grupo);
        }

        echo "Grupos criados com sucesso!";
    }
}
