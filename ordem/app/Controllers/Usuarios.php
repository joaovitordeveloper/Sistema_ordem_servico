<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Usuario;
use App\Models\GrupoModel;
use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;

class Usuarios extends BaseController
{

    private $usuarioModel;
    private $grupoUsuarioModel;
    private $grupoModel;
    private $grupoCliente = 2;
    private $grupoAdmin   = 1;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->grupoUsuarioModel = new GrupoUsuarioModel();
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
            'titulo' => 'Lista de usuários',
        ];

        return \view('Usuarios/index', $data);
    }

    /**
     * Função responsavel pela apresentação dos dados dos usuários na pagina inicial
     *
     * @return void
     */
    public function recuperaUsuarios()
    {
        //valida se e uma requisição via ajax
        if (!$this->request->isAJAX()) {
            return \redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'email',
            'ativo',
            'imagem',
            'deletado_em',
        ];

        $usuarios = $this->usuarioModel->select($atributos)->withDeleted(true)->orderBy('id', 'DESC')->findAll();
        //recebera o array de objetos para retornar conforme a documentação do dataTable
        $data = [];

        foreach ($usuarios as $usuario) {

            $imagem = [
                'src' => site_url("recursos/img/usuario_sem_imagem.png"),
                'class' => 'rounded-circle img-fluid',
                'alt' => \esc($usuario->nome),
                'width' => '50',
            ];

            if ($usuario->imagem != null) {
                $imagem = [
                    'src' => site_url("usuarios/imagem/$usuario->imagem"),
                    'class' => 'rounded-circle img-fluid',
                    'alt' => 'Usuário sem imagem',
                    'width' => '50',
                ];
            }
            $data[] = [
                'imagem' => $usuario->imagem = img($imagem),
                'nome' => \anchor("usuarios/exibir/$usuario->id", esc($usuario->nome), 'title="Exibir usuário ' . esc($usuario->nome) . '""'),
                'email' => esc($usuario->email), //função esc para validar caracteres especiais propria do CI4
                'ativo' => $usuario->exibeSituacao(),
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    /**
     * Função para fazer a chamada da exibição da view de exibição do usuário
     *
     * @param integer|null $id
     * @return void
     */
    public function exibir(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $data = [
            'titulo' => "Detalhando o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return \view('Usuarios/exibir', $data);
    }

    /**
     * função para fazer a chamada da view de edição do usuário.
     *
     * @param integer|null $id
     * @return void
     */
    public function editar(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $data = [
            'titulo' => "Editando o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return \view('Usuarios/editar', $data);
    }

    /**
     * Função para fazer o tratamento dos dados e atualizar o usuário.
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
        $usuario = $this->buscaUsuarioOu404($post['id']);

        if (empty($post['password'])) {
            unset($post['password']);
            unset($post['password_confirmation']);
        }

        $usuario->fill($post); //preenchendo os atributos do usuario.

        if ($usuario->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->protect(false)->save($usuario)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        return $this->response->setJSON($retorno);
    }

    /**
     * função para fazer a chamada da view de criação de um novo Usuário.
     *
     * @return void
     */
    public function criar()
    {
        $usuario = new Usuario();
        $data = [
            'titulo' => "Criando novo usuário ",
            'usuario' => $usuario,
        ];

        return \view('Usuarios/criar', $data);
    }

    /**
     * Função para tratar os dados e realizar o cadastro de um novo usuário.
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
        $usuario = new Usuario($post); //criando novo objeto da entidade usuario

        if ($this->usuarioModel->protect(false)->save($usuario)) {
            $btnCriar = anchor("usuarios/criar", 'Novo Usuário', ['class' => 'btn btn-danger mt-2']);
            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");
            $retorno['id'] = $this->usuarioModel->getInsertID(); //retorna o ultimo id inserido na tabela.
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        return $this->response->setJSON($retorno);
    }

    /**
     * Função para fazer a chamada da view de edição de imagem
     *
     * @param integer|null $id
     * @return void
     */
    public function editarImagem(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $data = [
            'titulo' => "Alterando a imagem usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return \view('Usuarios/editar_imagem', $data);
    }

    /**
     * Realiza a tratativa do arquivo de imagem do usuário e salva.
     *
     * @return void
     */
    public function upload()
    {
        //valida se e uma requisição via ajax
        if (!$this->request->isAJAX()) {
            return \redirect()->back();
        }

        //envio do hash do token do form.
        $retorno['token'] = csrf_hash();

        //validando arquivos.
        $validacao = service('validation');

        $regras = [
            'imagem' => 'uploaded[imagem]|max_size[imagem,1024]|ext_in[imagem,png,jpg,jpeg,webp]',
        ];

        $mensagens = [ // Mensagens de retorno na validação
            'imagem' => [
                'uploaded' => 'Por favor escolha uma imagem.',
                'ext_in' => 'Por favor escolha uma imagem png, jpg, jpeg ou webp.',
                'max_size' => 'Por favor escolha uma imagem de no máximo 1024.',
            ],
        ];
        $validacao->setRules($regras, $mensagens);

        if (!$validacao->withRequest($this->request)->run()) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
            $retorno['erros_model'] = $validacao->getErrors();

            return $this->response->setJSON($retorno);
        }

        //pegando os dados da requisição.
        $post = $this->request->getPost();
        $usuario = $this->buscaUsuarioOu404($post['id']);

        $imagem = $this->request->getFile('imagem'); //pegando a imagem do post
        list($largura, $altura) = getimagesize($imagem->getPathName());

        if ($largura < '300' || $altura < '300') {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
            $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor do que 300 x 300 pixels.'];

            return $this->response->setJSON($retorno);
        }

        $caminhoImagem = $imagem->store('usuarios');
        $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

        $this->manipulaImagem($caminhoImagem, $usuario->id);

        //atualizando a tabela de usuários.
        $imagemAntiga = $usuario->imagem; //pegando a imagem antiga se tiver.

        $usuario->imagem = $imagem->getName();
        $this->usuarioModel->save($usuario);

        if ($imagemAntiga != null) {
            $this->removeImagemDoFileSystem($imagemAntiga);
        }

        session()->setFlashdata('sucesso', 'Imagem atualizada com sucesso!');

        return $this->response->setJSON($retorno);
    }

    /**
     * Realizada a mostragem da imagem do usuário.
     *
     * @param string|null $imagem
     * @return void
     */
    public function imagem(string $imagem = null)
    {
        if ($imagem != null) {
            $this->exibeArquivo('usuarios', $imagem);
        }
    }

    /**
     * De acordo com o metodo passado realiza a função de exibir a view de excluir o usuario ou realiza a propria exclusão do usuário.
     *
     * @param integer|null $id
     * @return void
     */
    public function excluir(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        if ($this->request->getMethod() === 'post') {

            $this->usuarioModel->delete($usuario->id);

            if ($usuario->deletado_em != null) {
                return redirect()->back()->with('info', "Esse usuário já encontra-se excluido");
            }

            if ($usuario->imagem != null) {
                $this->removeImagemDoFileSystem($usuario->imagem);
            }

            $usuario->imagem = null;
            $usuario->ativo = false;

            $this->usuarioModel->protect(false)->save($usuario);
            //retorno a baixo so funciona quando não e utilizado ajax request
            return redirect()->to(\site_url("usuarios"))->with('sucesso', "Usuário $usuario->nome excluido com sucesso!");
        }

        $data = [
            'titulo' => "Excluindo o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return \view('Usuarios/excluir', $data);
    }

    /**
     * Recupera o usuario que já foi deletado.
     *
     * @param integer|null $id
     * @return void
     */
    public function restaurarUsuario(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas usuários excluidos podem ser recuperados");
        }

        $usuario->deletado_em = null;
        $this->usuarioModel->protect(false)->save($usuario);

        return redirect()->back()->with('sucesso', "Usuário $usuario->nome recuperado com sucesso!");
    }

    /**
     * Método que fará a busca dos grupos de acesso do usuário.
     *
     * @param integer|null $id
     * @return void
     */
    public function grupos(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $usuario->grupos = $this->grupoUsuarioModel->recuperaGruposDoUsuario($usuario->id, 5);
        $usuario->pager = $this->grupoUsuarioModel->pager;
        $grupoCliente = 2;
        $grupoAdmin = 1;

        $data = [
            'titulo' => "Gerenciando os grupos de acesso do usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        //quando o usuário for um cliente irá retornar para a view do usuário informando que ele e um cliente 
        if (in_array($this->grupoCliente, array_column($usuario->grupos, 'grupo_id'))) {
            return \redirect()->to(\site_url("usuarios/exibir/$usuario->id"))
                              ->with('info', "Esse usário é um cliente, portanto, não é necessário atribuí-lo ou removê-lo de outros grupos de acesso");
        }
 
        if (in_array($this->grupoAdmin, array_column($usuario->grupos, 'grupo_id'))) {
            $usuario->full_control = true;
            return \view('Usuarios/grupos', $data);
        }

        $usuario->full_control = false;

        if (!empty($usuario->grupos)) {
            $gruposExistentes = \array_column($usuario->grupos, 'grupo_id');
            $data['gruposDisponiveis'] = $this->grupoModel->where('id !=', 2)->whereNotIn('id', $gruposExistentes)->findAll();
        
        }else {
            $data['gruposDisponiveis'] = $this->grupoModel->where('id !=', 2)->findAll();
        }

        return \view('Usuarios/grupos', $data);
    }

    /**
     * função responsavel por salvar o grupo do usuário.
     *
     * @return void
     */
    public function salvarGrupos()
    {
         //envio do hash do token do form.
         $retorno['token'] = csrf_hash();

         //pegando os dados da requisição.
         $post = $this->request->getPost();
         $usuario = $this->buscaUsuarioOu404($post['id']);

         if (empty($post['grupo_id'])) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
            $retorno['erros_model'] = ['grupo_id' => 'Escolha uma ou mais grupos para salvar.'];
    
            return $this->response->setJSON($retorno);
        }

        if (in_array(2, $post['grupo_id'])) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
            $retorno['erros_model'] = ['grupo_id' => 'O grupo de clientes não pode ser atribuido de forma manual.'];
    
            return $this->response->setJSON($retorno);
        }

        if (in_array(1, $post['grupo_id'])) {
            $grupoAdmin = [
                'grupo_id' => 1,
                'usuario_id' => $usuario->id,
            ];

            $this->grupoUsuarioModel->insert($grupoAdmin);
            $this->grupoUsuarioModel->where('grupo_id !=', 1)->where('usuario_id', $usuario->id)->delete();
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            session()->setFlashdata('info', 'Notamos que o grupo Administrador foi informado, portanto, não há necessidade de informar outros grupos, pois apenas o Administrador será associado ao usuário!');
            return $this->response->setJSON($retorno);
        }

        $gruposPush = [];

        foreach ($post['grupo_id'] as $grupo) {
            array_push($gruposPush, [
                'grupo_id' => $grupo,
                'usuario_id' => $usuario->id,
            ]);
        }
        
        $this->grupoUsuarioModel->insertBatch($gruposPush);
        session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
        return $this->response->setJSON($retorno);
    }

    /**
     * Função responsavel por fazer a remoção de um grupo de acesso associado a um usuário.
     *
     * @return void
     */
    public function removeGrupo(int $id = null)
    {
        if ($this->request->getMethod() === 'post') {
            $grupoUsuario = $this->buscaGrupoUsuarioOu404($id);
            
            if ($grupoUsuario->grupo_id == 2) {
                return \redirect()->to(\site_url("usuarios/exibir/$grupoUsuario->usuario_id"))->with("info", "Não e permitida a exclusão do usuário do grupo de clientes");
            }

            $this->grupoUsuarioModel->delete($id);
            //retorno a baixo so funciona quando não e utilizado ajax request
            return redirect()->back()->with('sucesso', 'Usuário removido do grupo de acesso com sucesso!');
        }

        return \redirect()->back();
    }

    /**
     * Função para a edição de senha
     *
     * @return void
     */
    public function editarSenha()
    {
        $data = [
            'titulo' => 'Edite sua senha de acesso',
        ];

        return view('Usuarios/editar_senha', $data);
    }

    /**
     * Método para atualizar a senha do usuário.
     *
     * @return void
     */
    public function atualizarSenha()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $currentPassword = $this->request->getPost('current_password');
        $usuario = usuario_logado();

        if ($usuario->verificaPassword($currentPassword) === false) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
            $retorno['erros_model'] = ['current_password' => 'Senha atual inválida'];
            return $this->response->setJSON($retorno);
        }

        $usuario->fill($this->request->getPost());

        if ($usuario->hasChanged() === false) {
            $retorno['info'] = 'Não há dados para atualizar.';
            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->save($usuario)) {
            $retorno['sucesso'] = 'Senha atualizada com sucesso.';
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente.';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        return $this->response->setJSON($retorno);
    }

    /**
     * Metodo que recupera o usuario.
     *
     * @param integer|null $id
     * @return Exception|obeject
     */
    private function buscaUsuarioOu404(int $id = null)
    {
        //o metodo withDeleted busca todos os dados até mesmo os excluidos
        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }

        return $usuario;
    }

    /**
     * Metodo que recupera o registro do grupo associado ao usuário.
     *
     * @param integer|null $id
     * @return Exception|obeject
     */
    private function buscaGrupoUsuarioOu404(int $id = null)
    {
        //o metodo withDeleted busca todos os dados até mesmo os excluidos
        if (!$id || !$grupoUsuario = $this->grupoUsuarioModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o registro de associação ao grupo de acesso $id");
        }

        return $grupoUsuario;
    }

    /**
     * Realiza a exclusão da imagem
     *
     * @param string $imagem
     * @return void
     */
    private function removeImagemDoFileSystem(string $imagem)
    {
        $caminhoImagem = WRITEPATH . "uploads/usuarios/$imagem";

        if (is_file($caminhoImagem)) {
            unlink($caminhoImagem);
        }
    }

    /**
     * Função para a manipulação das imagens dos usuarios.
     *
     * @param string $caminhoImagem
     * @param integer $usuario_id
     * @return void
     */
    private function manipulaImagem(string $caminhoImagem, int $usuario_id)
    {
        //redimensionando a imagem e salvando de forma que substitui a que ja existe.
        service('image')
            ->withFile($caminhoImagem)
            ->fit(300, 300, 'center')
            ->save($caminhoImagem);

        //botando a marca d'agua de texto na imagem
        $anoAtual = date('Y');
        \Config\Services::image('imagick')
            ->withFile($caminhoImagem)
            ->text("Ordem $anoAtual - User-ID $usuario_id", [
                'color' => '#fff',
                'opacity' => 0.5,
                'withShadow' => false,
                'hAlign' => 'center',
                'vAlign' => 'bottom',
                'fontSize' => 10,
            ])
            ->save($caminhoImagem);
    }
}
