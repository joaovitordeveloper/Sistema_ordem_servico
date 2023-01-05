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
            'cores'  => $corModel->where('ativa', true)->findAll(1)
        ];
        return view('minha', $data);
    }
}
