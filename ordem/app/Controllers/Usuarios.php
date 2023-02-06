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

    public function recuperaUsuarios()
    {
        //valida se e uma requisição via ajax
        if (!$this->request->isAJAX()) {
            return \redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'email',
            'ativo',
            'imagem'
        ];

        $usuarios = $this->usuarioModel->select($atributos)->findAll();
        //recebera o array de objetos para retornar conforme a documentação do dataTable
        $data = [];
        
        foreach ($usuarios as $usuario) {
           $data[] = [
                'imagem' => $usuario->imagem,
                'nome'   => esc($usuario->nome),//função esc para validar caracteres especiais propria do CI4
                'email'  => esc($usuario->email),
                'ativo'  => ($usuario->ativo == true ? 'Ativo' : '<span class = "text-warning">Inativo</span>')
           ]; 
        }

        return $this->response->setJSON(['data' => $data]);
    }
}
