<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Usuarios extends BaseController
{

    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Lista de usuários',
        ];

        return \view('Usuarios/index', $data);
    }
}
