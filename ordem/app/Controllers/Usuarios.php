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
                'nome'   => \anchor("usuarios/exibir/$usuario->id", esc($usuario->nome), 'title="Exibir usuário ' . esc($usuario->nome) . '""'),
                'email'  => esc($usuario->email), //função esc para validar caracteres especiais propria do CI4
                'ativo'  => ($usuario->ativo == true ? '<i class="fa fa-unlock-alt text-success"></i>&nbsp;Ativo' : '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo')
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function exibir(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $data = [
            'titulo'  => "Detalhando o usuário " . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return \view('Usuarios/exibir', $data);
    }

    public function editar(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $data = [
            'titulo'  => "Editando o usuário " . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return \view('Usuarios/editar', $data);
    }

    public function atualizar()
    {
        //valida se e uma requisição via ajax
        if (!$this->request->isAJAX()) {
            return \redirect()->back();
        }

        $retorno['token'] = csrf_hash();
        $retorno['erro'] = "Essa é uma mensagem de erro de validação";
        $retorno['erros_model'] = [
            'nome' => 'O nome é obrigatorio',
            'email' => 'Email invalido',
            'password' => 'A senha é muito curta',
        ];

        return $this->response->setJSON($retorno);

        $post = $this->request->getPost();
        echo '<pre>';
        print_r($post);exit;
    }

    /**
     * Metodo que recupera o usuario.
     *
     * @param integer|null $id
     * @return void
     */
    private function buscaUsuarioOu404(int $id = null)
    {
        //o metodo withDeleted busca todos os dados até mesmo os excluidos
        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }

        return $usuario;
    }
}
