<?php

namespace App\Models;

use App\Libraries\Token;
use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $returnType = 'App\Entities\Usuario';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'nome',
        'email',
        'password',
        'reset_hash',
        'reset_expira_em',
        'imagem',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField = 'criado_em';
    protected $updatedField = 'atualizado_em';
    protected $deletedField = 'deletado_em';

    // Validation
    //não pode ter espaços
    protected $validationRules = [
        'nome'         => 'required|min_length[3]|max_length[125]',
        'email'        => 'required|valid_email|max_length[230]|is_unique[usuarios.email,id,{id}]',
        'password'     => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]',
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome e obrigatório.',
            'min_length' => 'O campo Nome precisar ter pelo menos 3 caractéres.',
            'max_length' => 'O campo Nome não poder ser maior que 125 caractéres.',
        ],
        'email' => [
            'required' => 'O campo E-mail e obrigatório.',
            'is_unique' => 'Esse e-mail já existe no sistema.',
            'max_length' => 'O campo Nome não poder ser maior que 230 caractéres.',
        ],
        'password_confirmation' => [
            'required_with' => 'Por favor confirme sua senha.',
            'matches' => 'As senhas precisam combinar.',
        ],
    ];

    // Callbacks
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }

        return $data;
    }

    /**
     * Metodo que recupera o usuário para logar na aplicação
     *
     * @param string $email
     * @return null|object
     */
    public function buscaUsuarioPorEmail(string $email)
    {
        return $this->where('email', $email)->where('deletado_em', null)->first();
    }

    /**
     * Método que recupera as permissões do usuário logado
     *
     * @param integer $usuario_id
     * @return null|array
     */
    public function recuperaPermissoesDoUsuarioLogado(int $usuario_id)
    {
        $atributos = [
            'usuarios.id',
            'usuarios.nome AS usuario',
            'grupos_usuarios.*',
            'permissoes.nome AS permissao'
        ];

        return $this->select($atributos)
                    ->asArray()//recupera com o formato de array
                    ->join('grupos_usuarios', 'grupos_usuarios.usuario_id = usuario_id')
                    ->join('grupos_permissoes', 'grupos_permissoes.grupo_id = grupos_usuarios.grupo_id')
                    ->join('permissoes', 'permissoes.id = grupos_permissoes.permissao_id')
                    ->where('usuarios.id', $usuario_id)
                    ->groupBy('permissoes.nome')
                    ->findAll();
    }

    /**
     * Método que recupera o usuario de acordo com o hash do token
     *
     * @param Type|null $var
     * @return null|object
     */
    public function buscaUsuarioParaRedefinirSenha(string $token)
    {
        $token = new Token($token);
        $tokenHash = $token->getHash();

        $usuario = $this->where('reset_hash', $tokenHash)
                        ->where('deletado_em', null)
                        ->first();
                       
        if ($usuario === null) {
            return null;
        }      
        
        if ($usuario->reset_expira_em < date('Y-m-d H:i:s')) {
            return null;
        }

        return $usuario;
    }
}
