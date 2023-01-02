<?php

namespace App\Controllers;

use App\Controllers\BaseController;

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
        echo "esse e o metodo minha";
    }
}
