<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Usuario;
use App\Models\UsuarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class Usuarios extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index(): string
    {
        $data = [
            'titulo' => 'Listando os usuários do sistema'
        ];

        return view('usuarios/index', $data);
    }

    public function criar(): string
    {
        $usuario = new Usuario();

        $title = "Criar Novo Usuário";
        $data = ['titulo' => $title, 'usuario' => $usuario];

        return view('Usuarios/criar', $data);
    }

    public function exibir(int $id): string
    {
        $usuario = $this->buscaUsuarioOu404($id);
    
        $title = "Detalhes do Usuário: $usuario->nome";
        $data = ['titulo' => $title, 'usuario' => $usuario];

        return view('Usuarios/exibir', $data);
    }

    public function editar(int $id): string
    {
        $usuario = $this->buscaUsuarioOu404($id);
    
        $title = "Edição do Usuário: $usuario->nome";
        $data = ['titulo' => $title, 'usuario' => $usuario];

        return view('Usuarios/editar', $data);
    }

    public function editarImagem(int $id): string
    {
        $usuario = $this->buscaUsuarioOu404($id);
    
        $title = "Altareando a Imagem do Usuário: $usuario->nome";
        $data = ['titulo' => $title, 'usuario' => $usuario];

        return view('Usuarios/editar_imagem', $data);
    }

    public function recuperaUsuarios(): ResponseInterface
    {
        if (!$this->request->isAJAX())
            return redirect()->back();
        $attr = ['id', 'nome', 'email', 'ativo', 'imagem'];
        $usuarios = $this->usuarioModel->select($attr)->orderBy('nome', 'ASC')->findAll();  
        
        $data = [];

        foreach($usuarios as $usuario) {
            $nomeUsuario = esc($usuario->nome);

            // $imagem = null;
            if(!is_null($usuario->imagem)) {
                $imagem = [
                    'src' => site_url("usuarios/imagem/$usuario->imagem"), 
                    'class' => 'rounded img-fluid', 
                    'alt' => esc($usuario->nome), 
                    'width' => '50'
                ];
            } else {
                $imagem = [
                    'src' => site_url("recursos/img/user-icon.png"), 
                    'class' => 'rounded img-fluid', 
                    'alt' => 'Usuário sem imagem', 
                    'width' => '50'
                ];
            }

            $data[] = [
                'imagem' => img($imagem),
                'nome' => anchor("/usuarios/exibir/$usuario->id", $nomeUsuario, "title='Exibir $nomeUsuario'"),
                'email' => esc($usuario->email),
                'ativo' => ($usuario->ativo == true) ? '<i class="fa fa-unlock"></i>&nbsp;Ativo' : '<i class="fa fa-lock"></i>&nbsp;<span class="text-warning">Inativo</span>',
            ];
        }

        $response = ['data' => $data];
        return $this->response->setJSON($response);
    }

    public function cadastrar(): ResponseInterface
    {
        if (!$this->request->isAJAX())
            return redirect()->back();

        $response['token'] = csrf_hash();
        
        $post = $this->request->getPost();
        $usuario = new Usuario($post);

        if ($this->usuarioModel->protect(false)->save($usuario)) {
            session()->setFlashdata('sucesso', 
                'Usuário salvo com sucesso! <a href="' 
                . site_url('usuarios/criar') 
                . '" class="btn btn-danger ms-1">Deseja Criar Outro Novo Usuário?</a>');
            $response['id'] = $this->usuarioModel->getInsertID();
            return $this->response->setJSON($response);
        }
                    
        $response['erro'] = 'Por favor, verifique os erros abaixo e tente novamente';
        $response['erros_model'] = $this->usuarioModel->errors();

        return $this->response->setJSON($response);
    }

    public function atualizar(): ResponseInterface
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
            
        if ($this->usuarioModel->protect(false)->save($usuario)) {
            session()->setFlashdata('sucesso', 'Usuário atualizado com sucesso!');
            return $this->response->setJSON($response);
        }
            
        
        $response['erro'] = 'Por favor, verifique os erros abaixo e tente novamente';
        $response['erros_model'] = $this->usuarioModel->errors();

        return $this->response->setJSON($response);
    }

    public function upload()
    {
        if (!$this->request->isAJAX())
            return redirect()->back();

        $response['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'imagem' => 'uploaded[imagem]|max_size[imagem,2048]|ext_in[imagem,png,jpg,jpeg,webp]'
        ];

        $mensagens = [
           'imagem' => [
                'uploaded' => 'Por favor escolha uma imagem',
                'max_size' => 'O tamanho máximo da imagem aceito é 2 MB',
                'ext_in' => 'Apenas serão aceitos arquvios de imagem nos seguintes formatos: png, jpg, jep e webp',
           ]
        ];

        $validacao->setRules($regras, $mensagens);

        if (!$validacao->withRequest($this->request)->run()) {
            $response['erro'] = 'Por favor, verifique os erros abaixo e tente novamente';
            $response['erros_model'] = $validacao->getErrors();

            return $this->response->setJSON($response);
        }
        
        $post = $this->request->getPost();
        $imagem = $this->request->getFile('imagem');

        list($largura, $altura) = getimagesize($imagem->getPathname());

        if ($largura < '300' || $altura < '300') {
            $response['erro'] = 'Por favor, verifique os erros abaixo e tente novamente';
            $response['erros_model'] = ['dimensao' => 'A imamgem não pode ser menor que 300 x 300 pixels'];

            return $this->response->setJSON($response);
        }

        $imagemCaminho = $imagem->store('usuarios');
        $caminhoImagem = WRITEPATH . 'uploads\\' . $imagemCaminho;

        service('image')
            ->withFile($caminhoImagem)
            ->fit(300, 300, 'center')
            ->save($caminhoImagem);
        
        // A partir daqui podemos atualizar a tabela usuários
        $usuario = $this->buscaUsuarioOu404((int)$post['id']);
        $imagemAntiga = $usuario->imagem;
        if (!is_null($imagemAntiga))
            $this->removeImagem($imagemAntiga);

        $usuario->imagem = $imagem->getName();
        $this->usuarioModel->save($usuario);

        session()->setFlashdata('sucesso', 'Imagem Atualizada com Sucesso 😉');
        return $this->response->setJSON($response);
    } 

    public function excluir(int $id)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        if ($this->request->getMethod() === 'post') {
            
            if (!is_null($usuario->imagem))
                $this->removeImagem($usuario->imagem);

            $this->usuarioModel->delete($usuario->id);

            return redirect()->to(site_url('usuarios'))->with('sucesso', "Usuário $usuario->nome Excluído com Sucesso.");
        }
    
        $title = "Excluindo o Usuário: $usuario->nome";
        $data = ['titulo' => $title, 'usuario' => $usuario];

        return view('usuarios/excluir', $data);
    }

    public function imagem(string $imagem = null)
    {
        if (!is_null($imagem))
            return $this->exibeArquivo('usuarios', $imagem);
        return null;
    }

    private function buscaUsuarioOu404(int $id = null)
    {
        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id))
            throw PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");

        return $usuario;
    }

    private function removeImagem(string $imagem)
    {
        $caminhoImagem = WRITEPATH . 'uploads\\usuarios\\' . $imagem;
        if (is_file($caminhoImagem)) 
            return unlink($caminhoImagem);
    }
}
