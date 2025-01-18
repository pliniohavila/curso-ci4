<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Grupo;
use App\Models\GrupoModel;
use App\Models\GrupoPermissaoModel;
use App\Models\PermissaoModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

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

    public function index(): string
    {
        $data = [
            'titulo' => 'Listando os grupos e permissões do sistema'
        ];

        return view('Grupos/index', $data);
    }

    public function exibir(int $id): string
    {
        $grupo = $this->buscaGrupoOu404($id);

        $title = "Detalhes do Usuário: $grupo->nome";
        $data = ['titulo' => $title, 'grupo' => $grupo];

        return view('Grupos/exibir', $data);
    }

    public function criar()
    {
        $grupo = new Grupo();

        $title = 'Criando novo grupo acesso';
        $data = ['titulo' => $title, 'grupo' => $grupo];

        return view('Grupos/criar', $data);
    }

    public function editar(int $id)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id == 1)
            return redirect()->back()->with('atencao', 'Não é possível editar o grupo:' . $grupo->nome);

        $title = "Edição do Grupo: $grupo->nome";
        $data = ['titulo' => $title, 'grupo' => $grupo];

        return view('Grupos/editar', $data);
    }

    public function excluir(int $id)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($this->request->getMethod() === 'post') {
            $this->grupoModel->delete($grupo->id);
            return redirect()->to(site_url('grupos'))->with('sucesso', "Grupo $grupo->nome excluído com sucesso.");
        }

        $title = "Excluindo o Grupo: $grupo->nome";
        $data = ['titulo' => $title, 'grupo' => $grupo];

        return view('Grupos/excluir', $data);
    }

    public function permissoes(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if (($grupo->id == 1) || ($grupo->id == 2))
            return redirect()->back()->with('info', 'Não é possível alterar as permissões do grupo:' . $grupo->nome);

        if ($grupo->id > 2) {
            $grupo->permissoes = $this->grupoPermissaoModel->recuperaPermissoesDoGrupo($grupo->id, 5);
            $grupo->pager = $this->grupoPermissaoModel->pager;
        }

        $title = 'Gerenciando as permissões do grupo de acesso: ' . esc($grupo->nome);
        $data = ['titulo' => $title, 'grupo' => $grupo];

        if (!empty($grupo->permissoes)) {
            $permissoesExistentes = array_column($grupo->permissoes, 'permissao_id');
            $data['permissoesDisponiveis'] = $this->permissaoModel->whereNotIn('id', $permissoesExistentes)->findAll();
        } else {
            $data['permissoesDisponiveis'] = $this->permissaoModel->findAll();
        }

        return view('Grupos/permissoes', $data);
    }

    public function salvarPermissoes(): ResponseInterface
    {
        if (!$this->request->isAJAX())
            return redirect()->back();

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $grupo = $this->buscaGrupoOu404($post['id']);

        if (empty($post['permissao_id'])) {
            $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['permissao_id' => 'Escolha uma ou mais permissão para salvar'];
            return $this->response->setJSON($retorno);
        }

        $permissoesPush = [];
        foreach ($post['permissao_id'] as $permissao) {
            array_push($permissoesPush, [
                'grupo_id' => $grupo->id,
                'permissao_id' => $permissao
            ]);
        }

        $this->grupoPermissaoModel->insertBatch($permissoesPush);

        session()->setFlashData('sucesso', 'Permissões alteradas com sucesso!');
        return $this->response->setJSON($retorno);   
    }

    public function removePermissao(int $permissaoId)
    {
        $retorno['token'] = csrf_hash();
        
        $permissao = $this->grupoPermissaoModel->find($permissaoId);

        if (!$permissao)
            return redirect()->back()->with('error', 'Permissão não encontrada');

        $this->grupoPermissaoModel->delete($permissaoId);
        return redirect()->back()->with('sucesso', "Permissão removida com sucesso.");
    }

    public function cadastrar(): ResponseInterface
    {
        if (!$this->request->isAJAX())
            return redirect()->back();

        $response['token'] = csrf_hash();

        $post = $this->request->getPost();
        $grupo = new Grupo($post);

        if ($this->grupoModel->save($grupo)) {
            $btnCriar = anchor('grupos/criar', 'Cadastrar novo grupo de acesso', ['class' => 'btn btn-danger mt-2']);
            session()->setFlashdata('sucesso', "Dados salvos com sucesso! $btnCriar");
            $response['id'] = $this->grupoModel->getInsertID();
            return $this->response->setJSON($response);
        }

        $response['erro'] = 'Por favor, verifique os erros abaixo e tente novamente';
        $response['erros_model'] = $this->grupoModel->errors();

        return $this->response->setJSON($response);
    }

    public function recuperaGrupos(): ResponseInterface
    {
        if (!$this->request->isAJAX())
            return redirect()->back();
        $attr = ['id', 'nome', 'descricao', 'exibir'];
        $grupos = $this->grupoModel->select($attr)->orderBy('nome', 'ASC')->findAll();

        $data = [];

        foreach ($grupos as $grupo) {
            $nomeGrupo = esc($grupo->nome);

            $data[] = [
                'nome' => anchor("/grupos/exibir/$grupo->id", $nomeGrupo, "title='Exibir $nomeGrupo'"),
                'descricao' => esc($grupo->descricao),
                'exibir' => ($grupo->exibir) ? 'Sim' : 'Não'
            ];
        }

        $response = ['data' => $data];
        return $this->response->setJSON($response);
    }

    public function atualizar(): ResponseInterface
    {
        if (!$this->request->isAJAX())
            return redirect()->back();

        $response['token'] = csrf_hash();

        $post = $this->request->getPost();

        $grupo = $this->buscaGrupoOu404($post['id']);

        if ($grupo->id == 1) {
            $response['erro'] = 'Por favor, verifique os erros abaixo e tente novamente';
            $response['erros_model'] = ['grupo' => 'Não é possível editar o grupo:' . $grupo->nome];
            return $this->response->setJSON($response);
        }

        $grupo->fill($post);
        if (!$grupo->hasChanged()) {
            $response['info'] = 'Não há novos dados para serem atualizados!';
            return $this->response->setJSON($response);
        }

        if ($this->grupoModel->protect(false)->save($grupo)) {
            session()->setFlashdata('sucesso', 'Grupo atualizado com sucesso!');
            return $this->response->setJSON($response);
        }


        $response['erro'] = 'Por favor, verifique os erros abaixo e tente novamente';
        $response['erros_model'] = $this->grupoModel->errors();

        return $this->response->setJSON($response);
    }

    private function buscaGrupoOu404(int $id = null)
    {
        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id))
            throw PageNotFoundException::forPageNotFound("Não encontramos o grupo com $id");

        return $grupo;
    }
}
