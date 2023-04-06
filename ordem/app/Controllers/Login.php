<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Login extends BaseController
{
    public function novo()
    {
        $data = [
            'titulo' => 'Realize o login',
        ];

        return view('Login/novo', $data);
    }

    public function logar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $autenticacao = service('autenticacao');

        if ($autenticacao->login($email, $password) === false) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
            $retorno['erros_model'] = ['credenciais' => 'Não encontramos suas credenciais de acesso.'];
            return $this->response->setJSON($retorno);
        }

        $usuarioLogado = $autenticacao->pegaUsuarioLogado();
        session()->setFlashdata('sucesso', "Olá $usuarioLogado->nome, que bom que esta de volta!");

        if ($usuarioLogado->is_cliente) {
            $retorno['redirect'] = 'ordens/minhas';
            return $this->response->setJSON($retorno);
        }

        $retorno['redirect'] = 'home';
        return $this->response->setJSON($retorno);
    }
}
