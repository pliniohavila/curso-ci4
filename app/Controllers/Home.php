<?php

namespace App\Controllers;

use App\Libraries\Auth;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'titulo' => 'Home'
        ];
        return view('Home/index', $data);
    }

    public function login()
    {
        // $auth = service('auth');

        // $auth->login('huel.prudence@yahoo.com', '123456');    // Brody Bartell 62 todas as permissoes
        // $auth->login('turner.darrin@gmail.com', '123456'); // cliente
        // $auth->login('llewellyn21@eichmann.com', '123456'); // admin
        // $auth->login('ckemmer@gmail.com', '123456'); // atendente
        // $auth->login('ndoyle@padberg.org', '123456'); // sem permissoes

        // $usuario = $auth->pegaUsuarioLogado();
        // dd($usuario);
        // dd($usuario->temPermissaoPara('criar-usuarios'));
        // dd($auth->eCliente());

        // $auth->logout();
        // return redirect()->to(site_url('/'));
    }    
}
