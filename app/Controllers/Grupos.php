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

    public function editar(int $id): string|RedirectResponse 
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id == 1)
            return redirect()->back()->with('atencao', 'Não é possível editar o grupo:' . $grupo->nome);
    
        $title = "Edição do Grupo: $grupo->nome";
        $data = ['titulo' => $title, 'grupo' => $grupo];

        return view('Grupos/editar', $data);
    }

    public function recuperaGrupos(): ResponseInterface
    {
        if (!$this->request->isAJAX())
            return redirect()->back();
        $attr = ['id', 'nome', 'descricao', 'exibir'];
        $grupos = $this->grupoModel->select($attr)->orderBy('nome', 'ASC')->findAll();  
        
        $data = [];

        foreach($grupos as $grupo) {
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
    private function buscaGrupoOu404(int $id = null)
    {
        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id))
            throw PageNotFoundException::forPageNotFound("Não encontramos o grupo com $id");

        return $grupo;
    }
}
