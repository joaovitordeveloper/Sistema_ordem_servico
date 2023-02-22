<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Entities\Usuario;
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

        $usuarios = $this->usuarioModel->select($atributos)->orderBy('id', 'DESC')->findAll();
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

        //envio do hash do token do form.
        $retorno['token'] = csrf_hash();

        //pegando os dados da requisição.
        $post = $this->request->getPost();
        $usuario = $this->buscaUsuarioOu404($post['id']);

        if(empty($post['password'])){
            unset($post['password']);
            unset($post['password_confirmation']);
        }

        $usuario->fill($post);//preenchendo os atributos do usuario.

        if($usuario->hasChanged() == false){
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if($this->usuarioModel->protect(false)->save($usuario)){
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
        $retorno['erros_model'] = $this->usuarioModel->errors();
       
        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $usuario = new Usuario();
        $data = [
            'titulo'  => "Criando novo usuário ",
            'usuario' => $usuario
        ];

        return \view('Usuarios/criar', $data);
    }

    public function cadastrar()
    {
        //valida se e uma requisição via ajax
        if (!$this->request->isAJAX()) {
            return \redirect()->back();
        }

        //envio do hash do token do form.
        $retorno['token'] = csrf_hash();

        //pegando os dados da requisição.
        $post = $this->request->getPost();
        $usuario = new Usuario($post);//criando novo objeto da entidade usuario

        if($this->usuarioModel->protect(false)->save($usuario)){
            $btnCriar = anchor("usuarios/criar", 'Novo Usuário', ['class' => 'btn btn-danger mt-2']);
            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");
            $retorno['id'] = $this->usuarioModel->getInsertID();//retorna o ultimo id inserido na tabela.
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
        $retorno['erros_model'] = $this->usuarioModel->errors();
       
        return $this->response->setJSON($retorno);
    }

    public function editarImagem(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $data = [
            'titulo'  => "Alterando a imagem usuário " . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return \view('Usuarios/editar_imagem', $data);
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
