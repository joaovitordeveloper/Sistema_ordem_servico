<?php

namespace App\Libraries;

use App\Models\UsuarioModel;

class Autenticacao
{
    private $usuario;
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
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
}