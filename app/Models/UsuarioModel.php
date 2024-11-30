<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $returnType       = 'App\Entities\Usuario';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nome', 'email', 'password', 'reset_hash', 'reset_expira_em', 'imagem'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'id'           => 'permit_empty|is_natural_no_zero',
        'nome'         => 'required|min_length[3]|max_length[125]',
        'email'        => 'required|valid_email|max_length[230]|is_unique[usuarios.email,id,{id}]', // Não pode ter espaços
        'password'     => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]'
    ];

    protected $validationMessages  = [
        'id' => [
            'permit_empty' => 'O campo ID pode ser deixado em branco.',
            'is_natural_no_zero' => 'O campo ID deve conter um número natural maior que zero.'
        ],
        'nome' => [
            'required' => 'O campo Nome é obrigatório.', 
            'min_length' => 'O campo Nome deve contar com mais que 3 caracteres.', 
            'max_length' => 'O campo Nome não pode contar mais que 125 caracteres.', 
        ],
        'email' => [
            'required' => 'O campo Email é obrigatório.',
            'valid_email' => 'O campo Email deve conter um endereço de email válido.',
            'max_length' => 'O campo Email não pode ter mais que 230 caracteres.',
            'is_unique' => 'O Email informado já está cadastrado.',
        ],
        'password' => [
            'required' => 'O campo Senha é obrigatório.',
            'min_length' => 'O campo Senha deve conter pelo menos 6 caracteres.',
        ],
        'password_confirmation' => [
            'required_with' => 'O campo Confirmação de Senha é obrigatório quando a Senha é informada.',
            'matches' => 'O campo Confirmação de Senha deve ser igual ao campo Senha.',
        ],
    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];


    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }
        return $data;
    }
}
