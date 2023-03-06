<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoModel extends Model
{
    protected $table = 'grupos';
    protected $returnType = 'App\Entities\Grupo';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['nome', 'descricao', 'exibir'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField = 'criado_em';
    protected $updatedField = 'atualizado_em';
    protected $deletedField = 'deletado_em';

    protected $validationRules = [
        'nome' => 'required||max_length[125]|is_unique[grupos.nome,id,{id}]',
        'descricao' => 'required|max_length[240]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome e obrigatório.',
        ],
    ];
}
