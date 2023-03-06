<?php

namespace App\Controllers;

use App\Entities\Grupo;
use App\Models\GrupoModel;
use App\Controllers\BaseController;

class Grupos extends BaseController
{
    private $grupoModel;

    public function __construct()
    {
        $this->grupoModel = new GrupoModel();
    }

   /**
     * Função de inicio do controller que retorna a pagina inicial do modulo
     *
     * @return void
     */
    public function index()
    {
        $data = [
            'titulo' => 'Lista de grupos de acesso',
        ];

        return \view('Grupos/index', $data);
    }

    /**
     * Função responsavel pela apresentação dos dados dos grupos na pagina inicial
     *
     * @return void
     */
    public function recuperaGrupos()
    {
        //valida se e uma requisição via ajax
        if (!$this->request->isAJAX()) {
            return \redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'descricao',
            'exibir',
            'deletado_em',
        ];

        $grupos = $this->grupoModel->select($atributos)->withDeleted(true)->orderBy('id', 'DESC')->findAll();
        //recebera o array de objetos para retornar conforme a documentação do dataTable
        $data = [];

        foreach ($grupos as $grupo) {

            $data[] = [
                'nome' => \anchor("grupos/exibir/$grupo->id", esc($grupo->nome), 'title="Exibir grupo ' . esc($grupo->nome) . '""'),
                'descricao' => esc($grupo->descricao), //função esc para validar caracteres especiais propria do CI4
                'exibir' => ($grupo->exibir == true ? '<i class="fa fa-eye text-secondary"></i>&nbsp;Exibir grupo' : '<i class="fa fa-eye-slash text-danger"></i>&nbsp;Não exibir grupo'),
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }
}
?>
