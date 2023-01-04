<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CorModel;

class Teste extends BaseController
{
    public function index()
    {
        $data = [
            'cores' => ['azul', 'branco', 'vermelho', 'preto']
        ];
        return view('teste', $data);
    }

    public function minha()
    {
        $corModel = new CorModel();
        $data = [
            'titulo' => 'Buscando dados no banco',
            'cores'  => $corModel->findAll()
        ];
        return view('minha', $data);
    }
}
