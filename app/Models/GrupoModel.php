<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoModel extends Model
{
    protected $table            = 'grupos';
    protected $returnType       = 'App\Entities\Grupo';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['nome', 'descricao', 'exibir'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'id'           => 'permit_empty|is_natural_no_zero',
        'nome'         => 'required|min_length[3]|max_length[125]|is_unique[grupos.nome,id,{id}]',
        'descricao'        => 'required|max_length[240]|'
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
            'is_unique' => 'O Grupo informado já está cadastrado.',
        ],
        'descricao' => [
            'required' => 'O campo Email é obrigatório.',
            'max_length' => 'O campo Email não pode ter mais que 240 caracteres.',
            
        ]
    ];
}
