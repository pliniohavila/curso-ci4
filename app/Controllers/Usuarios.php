<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\Exceptions\DataException;

class Usuarios extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os usuários do sistema'
        ];

        return view('Usuarios/index', $data);
    }

    public function recuperaUsuarios() 
    {
        if (!$this->request->isAJAX())
            return redirect()->back();
        $attr = ['id', 'nome', 'email', 'ativo', 'imagem'];
        $usuarios = $this->usuarioModel->select($attr)->findAll();  
        
        $data = [];

        foreach($usuarios as $usuario) {
            $nomeUsuario = esc($usuario->nome);
            $data[] = [
                'imagem' => $usuario->imagem,
                'nome' => anchor("/usuarios/exibir/$usuario->id", $nomeUsuario, "title='Exibir $nomeUsuario'"),
                'email' => esc($usuario->email),
                'ativo' => ($usuario->ativo == true) ? '<i class="fa fa-unlock"></i>&nbsp;Ativo' : '<i class="fa fa-lock"></i>&nbsp;<span class="text-warning">Inativo</span>',
            ];
        }

        $response = ['data' => $data];
        return $this->response->setJSON($response);
    }

    public function exibir(int $id)
    {
        $usuario = $this->buscaUsuarioOu404($id);
    
        $title = "Detalhes do Usuário: $usuario->nome";
        $data = ['titulo' => $title, 'usuario' => $usuario];

        return view('Usuarios/exibir', $data);
    }

    public function editar(int $id)
    {
        $usuario = $this->buscaUsuarioOu404($id);
    
        $title = "Edição do Usuário: $usuario->nome";
        $data = ['titulo' => $title, 'usuario' => $usuario];

        return view('Usuarios/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX())
            return redirect()->back();

        $response['token'] = csrf_hash();
        
        $post = $this->request->getPost();
        if (empty($post['password'])) {
            unset($post['password']);
            unset($post['password_confirmation']);
        }

        $usuario = $this->buscaUsuarioOu404($post['id']);
        $usuario->fill($post);
        if (!$usuario->hasChanged()) {
            $response['info'] = 'Não há novos dados para serem atualizados!';
            return $this->response->setJSON($response);   
        }
            
        if ($this->usuarioModel->protect(false)->save($usuario)) 
            return $this->response->setJSON($response);
        
        $response['erro'] = 'Por favor, verifique os erros abaixo e tente novamente';
        $response['erros_model'] = $this->usuarioModel->erros();
        return $this->response->setJSON($response);
    }

    public function editarImagem(int $id)
    {
        $usuario = $this->buscaUsuarioOu404($id);
    
        $title = "Detalhes do Usuário: $usuario->nome";
        // $data = ['titulo' => $title, 'usuario' => $usuario];

        // return view('Usuarios/exibir', $data);
    }


    private function buscaUsuarioOu404(int $id = null)
    {
        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id))
            throw PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");

        return $usuario;
    }
}
