<?php

namespace App\Models;

use Core\Database\Model;

class Atleta extends Model
{
    protected ?string $table = 'atletas';
    protected array $fillable = [
        'nome_completo', 
        'data_nascimento', 
        'cpf', 
        'numero_registro', 
        'usuario_id', 
        'equipe_id', 
        'categoria_id', 
        'ativo'
    ];
}
