<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends Entity
{
    protected $dates   = [
        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];

    /**
     * Retorna a situação na qual o usuário se encontra
     *
     * @return void
     */
    public function exibeSituacao()
    {
        if($this->deletado_em) {
            $icone = '<span class="text-white">Excluido</span>&nbsp<i class="fa fa-undo"></i>&nbsp;Restaurar';
            $situacao = anchor("usuarios/restaurarUsuario/$this->id", $icone, ['class' => 'btn btn-outline-succes btn-sm']);

            return $situacao;
        }

        if($this->ativo == true) {
            return '<i class="fa fa-unlock-alt text-success"></i>&nbsp;Ativo';
        }

        if($this->ativo == false) {
            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo';
        }
    }

    /**
     * Método que verifica se a senha e valida
     *
     * @param string $password
     * @return boolean
     */
    public function verificaPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Método que valida se o usuario logado possui permissão para visualizar ou acessar determinada rota.
     *
     * @param string $permissao
     * @return boolean
     */
    public function temPermissaoPara(string $permissao): bool
    {
        if ($this->is_admin == true) {
            return true;
        }

        if (empty($this->permissoes)) {
            return false;
        }

        if (in_array($permissao, $this->permissoes) == false) {
            return false;
        }

        return true;
    }
}
