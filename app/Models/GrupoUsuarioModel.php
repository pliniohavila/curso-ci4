<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoUsuarioModel extends Model
{
    protected $table            = 'grupos_usuarios';
    protected $returnType       = 'object';
    protected $allowedFields    = ['grupo_id', 'usuario_id'];
    
    /**
     * Método que recupera os grupos de acesso do usuário informado.
     *
     * @param  integer $usuarioId
     * @param  integer $pagina
     * @return array|null
     */
    public function recuperaGruposDoUsuario(int $usuarioId, int $paginacao = 5)
    {
        $attr = [
            'grupos_usuarios.id AS principal_id', 
            'grupos.id AS grupo_id',
            'grupos.nome',
            'grupos.descricao'
        ];

        return $this
            ->select($attr)
            ->join('grupos', 'grupos.id = grupos_usuarios.grupo_id')
            ->join('usuarios', 'usuarios.id = grupos_usuarios.usuario_id')
            ->where('grupos_usuarios.usuario_id', $usuarioId)
            ->groupBy('grupos.nome')
            ->paginate($paginacao);
    }
    
    /**
     * Grupo que recupera o grupo no qual o usuário está logado. Utilizado apenas para verificar se 
     * o usuário é um cliente ou um administrador
     *
     * @param  integer $grupoId
     * @param  integer $usuarioId
     * @return null|object
     */
    public function usuarioEstaNoGrupo(int $grupoId, int $usuarioId)
    {
        return $this
            ->where('grupo_id', $grupoId)
            ->where('usuario_id', $usuarioId)
            ->first();
    }
}
