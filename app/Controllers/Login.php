<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Login extends BaseController
{
    public function novo()
    {
        $data = [
            'titulo' => 'Realize o Login', 
        ];

        return view('Login/novo', $data);
    }

    public function criar() 
    {
        $auth = service('autenticao');
        $retorno['token'] = csrf_hash();        

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (!($auth->login($email, $password))) {
            $retorno['erro'] = 'O e-mail ou senha informado está errado.';
            return $this->response->setJSON($retorno);
        }

        $usuarioLogado = $auth->pegaUsuarioLogado();
        session()->setFlashdata('sucesso', 'Olá ' . $usuarioLogado->nome . ', que bom que está de volta!');

        if ($usuarioLogado->eCliente) {
            $retorno['redirect'] = 'ordens/minhas';
            return $this->response->setJSON($retorno);
        }

        $retorno['redirect'] = '/';
        return $this->response->setJSON($retorno);
    }

    public function logout()
    {
        service('autenticao')->logout();
        return redirect()->to(site_url('/login'))->with('sucesso', "Esperamos ver você em breve 😉");
    }
}
