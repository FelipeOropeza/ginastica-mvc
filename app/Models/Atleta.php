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

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function equipe()
    {
        return $this->belongsTo(Equipe::class, 'equipe_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
