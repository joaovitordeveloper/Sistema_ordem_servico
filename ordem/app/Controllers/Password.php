<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Password extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function esqueci()
    {
        $data = [
            'titulo' => 'Esqueci a minha senha',
        ];

        return view('Password/esqueci', $data);
    }

    public function processaEsqueci()
    {
        //valida se e uma requisição via ajax
        if (!$this->request->isAJAX()) {
            return \redirect()->back();
        }

        //envio do hash do token do form.
        $retorno['token'] = csrf_hash();

        //pegando o e-mail da requisição.
        $email = $this->request->getPost('email');

        $usuario = $this->usuarioModel->buscaUsuarioPorEmail((string) $email);

        if ($usuario === null || $usuario->ativo === false) {
            $retorno['erro'] = 'Não encontramos uma conta válida com esse e-mail';
            return $this->response->setJSON($retorno);
        }

        $usuario->iniciaPasswordReset();
        
        $this->usuarioModel->save($usuario);

        //enviar e-mail

        return $this->response->setJSON([]);
    }

    public function resetEnviado()
    {
        $data = [
            'titulo' => 'E-mail de recuperação enviado para a sua caixa de entrada',
        ];

        return view('Password/reset_enviado', $data);
    }
}
