<?php

namespace App\Libraries;

class Token
{
  private $token;

  public function __construct(string $token = null)
  {
    if (!$token)
      $this->token = bin2hex(random_bytes(16));
    else
      $this->token = $token;
  }
  
  /**
   * Retorna o valor do token
   *
   * @return string token
   */
  public function getValue(): string
  {
    return $this->token;
  }

  public function getHash(): string
  {
    return hash_hmac("sha256", $this->token, getenv('CHAVE_RECUPERACAO_SENHA'));
  }
}