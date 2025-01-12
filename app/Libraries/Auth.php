<?php

namespace App\Libraries;

use App\Entities\Usuario;
use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;

class Auth
{
  private $usuario;
  private $usuarioModel;
  private $grupoUsuarioModel;

  public function __construct()
  {
    $this->usuarioModel = new UsuarioModel();
    $this->grupoUsuarioModel= new GrupoUsuarioModel();
  }

  /**
   * Método responsável pelo login na aplicação
   *
   * @param  string $email
   * @param  string $password
   * @return bool
   */
  public function login(string $email, string $password): bool
  {
    $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

    if (!$usuario) return false;
    
    if (!($usuario->verificaSenha($password))) return false;
    
    if (!($usuario->ativo)) return false;

    $this->logaUsuario($usuario);

    return true;
  }

  public function logout(): void
  {
    session()->destroy();
  }

  public function pegaUsuarioLogado()
  {
    if (!$this->usuario) $this->usuario = $this->pegaUsuarioDaSessao();

    return $this->usuario;
  }

  public function estaLogado()
  {   
    return $this->pegaUsuarioLogado();
  }

  /**
   * Recupera da sessão e valida o usuário logado
   *
   * @return null:object
   */
  private function pegaUsuarioDaSessao()
  {
    if (!session()->has('usuario_id')) return null;

    $usuario = $this->usuarioModel->find(session()->get('usuario_id'));

    if (!$usuario || !$usuario->ativo) return null;

    // Define permissões do usuário logado
    $usuario = $this->definePermissoesDoUsuarioLogado($usuario);

    return $usuario;
  }


  //  ------------------ Métodos Privados ------------------------ //

  private function logaUsuario(object $usuario): void
  {
    $session = session();

    $session->regenerate();

    // $_SESSION['__ci_last_regenerate'] = time(); // UTILIZEM essa instrução que o efeito é o mesmo e funciona perfeitamente.

    $session->set('usuario_id', $usuario->id);
    $session->set('usuario_name', $usuario->nome);
  }
  
  /**
   * Verifica se o usuário logado está vinculado ao grupo de admin
   *
   * @return bool
   */
  private function eAdmin(): bool
  {
    $grupoAdmin = 1;
    $administrador = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoAdmin, session()->get('usuario_id'));

    if (!$administrador) return false;
    
    return true;
  }

  private function eCliente(): bool 
  {
    $grupoCliente = 2;
    $cliente = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoCliente, session()->get('usuario_id'));

    if (!$cliente) return false;
    return true;
  }
  
  /**
   * Define as permissões do usuário logado
   *
   * @param  mixed $usuario
   * @return object
   */
  public function definePermissoesDoUsuarioLogado($usuario)
  {
    $usuario->eAdmin = $this->eAdmin();

    if ($usuario->eAdmin)
      $usuario->eCliente = false;
    else 
      $usuario->eCliente = $this->eCliente();

    if (!$usuario->eAdmin && !$usuario->eCliente)
      $usuario->permissoes = $this->recuperaPermissoesDoUsuario();
    
    return $usuario;
  }

  private function recuperaPermissoesDoUsuario(): array
  {
    $usuarioId = session()->get('usuario_id');
    $permissoesDoUsuario = $this->usuarioModel->recuperaPermissoesDoUsuarioLogado($usuarioId);
    return array_column($permissoesDoUsuario, 'permissao');
  }
}
