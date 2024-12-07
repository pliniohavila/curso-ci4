<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Grupo;
use App\Models\GrupoModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Grupos extends BaseController
{
    private $grupoModel;

    public function __construct()
    {
        $this->grupoModel = new GrupoModel();
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

    public function criar(): string|RedirectResponse
    {
        $grupo = new Grupo();

        $title = 'Criando novo grupo acesso';
        $data = ['titulo' => $title, 'grupo' => $grupo];

        return view('Grupos/criar', $data);
    }

    public function editar(int $id): string|RedirectResponse
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

        return view('grupos/excluir', $data);
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
