<?php

namespace App\Libraries;

use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;

class Autenticacao
{
    private $usuario;
    private $usuarioModel;
    private $grupoUsuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->grupoUsuarioModel = new GrupoUsuarioModel();
    }

    /**
     * Metodo que realiza o login na aplicação
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function login(string $email, string $password): bool
    {
       $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

       if ($usuario === null) {
            return false;
       }

       if ($usuario->verificaPassword($password) == false) {
            return false;
       }

       if ($usuario->ativo == false) {
            return false;
       }

       $this->logaUsuario($usuario);

       return true;
    }

    /**
     * Método de logOut
     *
     * @return void
     */
    public function logOut(): void
    {
        session()->destroy();
    }

    /**
     * Pega o usuário que vai esta logado.
     *
     * @return void
     */
    public function pegaUsuarioLogado()
    {
        if ($this->usuario === null) {
            $this->usuario = $this->pegaUsuarioDaSessao();
        }

        return $this->usuario;
    }

    /**
     * Metodo que verifica se o usuário esta logado
     *
     * @return boolean
     */
    public function estaLogado() :bool
    {
        return $this->pegaUsuarioLogado() !== null;
    }

    //-------------------- Métodos Privados ------------------------//

    /**
     * Método que insere na sessão o id do usuário.
     *
     * @param object $usuario
     * @return void
     */
    private function logaUsuario(object $usuario): void
    {
        $session = session();
        $session->regenerate();//gerando uma nova id para a sessão
        $session->set('usuario_id', $usuario->id);
    }

    /**
     * Método que recupera da sessão e valida o usuário logado.
     *
     * @return null|object
     */
    private function pegaUsuarioDaSessao()
    {
        if (session()->has('usuario_id') == false) {
            return null;
        }

        $usuario = $this->usuarioModel->find(session()->get('usuario_id'));

        if ($usuario == null || $usuario->ativo == false) {
            return null;
        }

        return $usuario;
    }

    /**
     * Verifica se o usuário logado faz parte do grupo admin
     *
     * @return boolean
     */
    private function isAdmin():bool
    {
        $grupoAdmin = 1;
        $administrador = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoAdmin, session()->get('usuario_id'));

        if ($administrador == null) {
            return false;
        }

        return true;

    }

    /**
     * Verifica se o usuário logado faz parte do grupo clientes.
     *
     * @return boolean
     */
    private function isCliente():bool
    {
        $grupoCliente = 2;
        $cliente = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoCliente, session()->get('usuario_id'));

        if ($cliente == null) {
            return false;
        }

        return true;

    }
}