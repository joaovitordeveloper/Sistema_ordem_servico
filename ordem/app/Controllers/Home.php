<?php

namespace App\Controllers;

use App\Libraries\Autenticacao;

class Home extends BaseController
{
    public function index()
    {
        //dd(usuario_logado());
        $data = [
            'titulo' => 'Home'
        ];

        return view('Home/index', $data);
    }

    public function login()
    {
        $autenticacao = service('autenticacao');

        $autenticacao->login('teste2@teste.com', '12345678');
        $usuario = $autenticacao->pegaUsuarioLogado();

        dd($usuario);
    }

    public function email()
    {
        $email = service('email');

        $email->setFrom('no-reply@allinsystems.com.br', 'Ordem de serviço LTDA');//de onde vai sair o e-mail
        $email->setTo('teste@allinsystems.com.br');//pra quem vai o e-mail

        $email->setSubject('Recuperação de senha');
        $email->setMessage('Iniciando a recuperação de senha');

        if ($email->send()) {
            echo "Email enviado";
        }else{
            echo $email->printDebugger();
        }
    }
}
