<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class Password extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function esqueci()
    {
        $data['titulo'] = 'Esqueci a minha senha 🫠';
        
        return view('Password/esqueci', $data);
    }

    public function processaEsqueci()
    {
        $retorno['token'] = csrf_hash();
        $post = $this->request->getPost();

        $email = $post['email'];

        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);
        if (!$usuario || !$usuario->ativo) {
            $retorno['erro'] = 'Não encontramos uma conta válida com o e-mail informado';
            return $this->response->setJSON($retorno);
        } 
        
        $usuario->iniciaPasswordReset();
        
        
        
    }
}
