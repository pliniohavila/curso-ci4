<?php 

if (!function_exists('usuario_logado')) {
  function usuario_logado() {
    return service('autenticao')->pegaUsuarioLogado();
  }
}