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
                'exibir' => $grupo->exibeSituacao(),
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

     /**
     * Função para fazer a chamada da exibição da view de exibição dos grupos
     *
     * @param integer|null $id
     * @return void
     */
    public function exibir(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);
        $data = [
            'titulo' => "Detalhando o grupo de acesso" . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return \view('Grupos/exibir', $data);
    }

    /**
     * Metodo que recupera o grupo de acesso.
     *
     * @param integer|null $id
     * @return void
     */
    private function buscaGrupoOu404(int $id = null)
    {
        //o metodo withDeleted busca todos os dados até mesmo os excluidos
        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o grupo de acesso $id");
        }

        return $grupo;
    }
}
?>
