<?php

namespace App\Controllers;

use App\Entities\Grupo;
use App\Models\GrupoModel;
use App\Controllers\BaseController;
use App\Models\GrupoPermissaoModel;
use App\Models\PermissaoModel;

class Grupos extends BaseController
{
    private $grupoModel;
    private $grupoPermissaoModel;
    private $permissaoModel;

    public function __construct()
    {
        $this->grupoModel = new GrupoModel();
        $this->grupoPermissaoModel = new GrupoPermissaoModel();
        $this->permissaoModel = new PermissaoModel();
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
            'titulo' => "Detalhando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return \view('Grupos/exibir', $data);
    }

     /**
     * Função para fazer a chamada da exibição da view de edição dos grupos
     *
     * @param integer|null $id
     * @return void
     */
    public function editar(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        //validando se não ha manipulação de usuário.
        if ($grupo->id < 3) {
            return \redirect()->back()->with('atencao', 'O grupo <b>' . \esc($grupo->nome) . '</b> não pode ser editado ou excluido, conforme detalhado na exibição do mesmo');
        }

        $data = [
            'titulo' => "Editando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return \view('Grupos/editar', $data);
    }

    /**
     * Função para fazer o tratamento dos dados e atualizar o grupo de acesso.
     *
     * @return void
     */
    public function atualizar()
    {
        //valida se e uma requisição via ajax
        if (!$this->request->isAJAX()) {
            return \redirect()->back();
        }

        //envio do hash do token do form.
        $retorno['token'] = csrf_hash();

        //pegando os dados da requisição.
        $post = $this->request->getPost();
        $grupo = $this->buscaGrupoOu404($post['id']);

        //validando se não ha manipulação de usuário.
        if ($grupo->id < 3) {
        
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
            $retorno['erros_model'] = ['grupo' => 'O grupo <b>' . \esc($grupo->nome) . '</b> não pode ser editado ou excluido, conforme detalhado na exibição do mesmo'];

            return $this->response->setJSON($retorno);
        }

        $grupo->fill($post); //preenchendo os atributos do grupo.

        if ($grupo->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->grupoModel->protect(false)->save($grupo)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
        $retorno['erros_model'] = $this->grupoModel->errors();

        return $this->response->setJSON($retorno);
    }

     /**
     * Função para fazer a chamada da exibição da view de criação dos grupos
     *
     * @param integer|null $id
     * @return void
     */
    public function criar()
    {
        $grupo = new Grupo();
        $data = [
            'titulo' => "Criando um novo grupo de acesso",
            'grupo' => $grupo,
        ];

        return \view('Grupos/criar', $data);
    }

    /**
     * Função para tratar os dados e realizar o cadastro de um novo grupo de acesso.
     *
     * @return void
     */
    public function cadastrar()
    {
        //valida se e uma requisição via ajax
        if (!$this->request->isAJAX()) {
            return \redirect()->back();
        }

        //envio do hash do token do form.
        $retorno['token'] = csrf_hash();

        //pegando os dados da requisição.
        $post = $this->request->getPost();
        $grupo = new Grupo($post); //criando novo objeto da entidade grupo

        if ($this->grupoModel->save($grupo)) {
            $btnCriar = anchor("grupos/criar", 'Novo Grupo', ['class' => 'btn btn-danger mt-2']);
            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");
            $retorno['id'] = $this->grupoModel->getInsertID(); //retorna o ultimo id inserido na tabela.
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
        $retorno['erros_model'] = $this->grupoModel->errors();

        return $this->response->setJSON($retorno);
    }

     /**
     * De acordo com o metodo passado realiza a função de exibir a view de excluir o grupo ou realiza a propria exclusão do grupo.
     *
     * @param integer|null $id
     * @return void
     */
    public function excluir(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        //validando se não ha manipulação de usuário.
        if ($grupo->id < 3) {
            return \redirect()->back()->with('atencao', 'O grupo <b>' . \esc($grupo->nome) . '</b> não pode ser editado ou excluido, conforme detalhado na exibição do mesmo');
        }

        if ($grupo->deletado_em != null) {
            return redirect()->back()->with('info', "Esse grupo já encontra-se excluido");
        }

        if ($this->request->getMethod() === 'post') {

            $this->grupoModel->delete($grupo->id);
            //retorno a baixo so funciona quando não e utilizado ajax request
            return redirect()->to(\site_url("grupos"))->with('sucesso', 'Grupo ' . esc($grupo->nome) . ' excluido com sucesso!');
        }

        $data = [
            'titulo' => "Excluindo o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return \view('Grupos/excluir', $data);
    }

    /**
     * Recupera o grupo que já foi deletado.
     *
     * @param integer|null $id
     * @return void
     */
    public function restaurarGrupo(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas grupos excluidos podem ser recuperados");
        }

        $grupo->deletado_em = null;
        $this->grupoModel->protect(false)->save($grupo);

        return redirect()->back()->with('sucesso', 'Grupo ' . esc($grupo->nome) . ' excluido com sucesso!');
    }

    /**
     * Função para fazer a chamada da exibição da view de gerenciamento de permissões dos grupos de acesso.
     *
     * @param integer|null $id
     * @return void
     */
    public function permissoes(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);
        //grupo administrador
        if ($grupo->id == 1) {
            return \redirect()->back()->with('info', 'Não e necessário atribuir ou remover permissões de acesso para o grupo <b>' . \esc($grupo->nome) . '</b>, pois esse grupo é Administrador.');
        }
        //grupo de clientes
        if ($grupo->id == 2) {
            return \redirect()->back()->with('info', 'Não e necessário atribuir ou remover permissões de acesso para o grupo de Clientes.');
        }

        if ($grupo->id > 2) {
            $grupo->permissoes = $this->grupoPermissaoModel->recuperaPermissoesDoGrupo($grupo->id, 5);
            $grupo->pager = $this->grupoPermissaoModel->pager;
        }

        $data = [
            'titulo' => "Gerenciando as permissões grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        if (!empty($grupo->permissoes)) {
            $permissoesExistentes = \array_column($grupo->permissoes, 'permissao_id'); 
            $data['permissoesDisponiveis'] = $this->permissaoModel->whereNotIn('id', $permissoesExistentes)->findAll();//buscando as permissões que o usuário ainda não possui
        }else{
            $data['permissoesDisponiveis'] = $this->permissaoModel->findAll();
        }

        return \view('Grupos/permissoes', $data);
    }

    /**
     * Metodo que recupera o grupo de acesso.
     * @param integer|null $id
     * @return object
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