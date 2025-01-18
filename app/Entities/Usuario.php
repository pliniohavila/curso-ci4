<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends Entity
{
    protected $datamap = [];
    protected $dates   = ['criado_em', 'atualizado_em', 'deletado_em_at'];
    protected $casts   = [];

    public function verificaPassword(string $senha): bool
    {
        return password_verify($senha, $this->password_hash);
    }
    
    /**
     * Método que valida o usuário para determinado recurso a especificar
     *
     * @param  string $permissao
     * @return bool
     */
    public function temPermissaoPara(string $permissao): bool 
    {
        if ($this->eAdmin) return true;

        if (empty($this->permissoes)) return false;

        if (!(in_array($permissao, $this->permissoes))) return false;

        return true;
    }
}
