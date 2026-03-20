<?php

namespace App\Models;

use Core\Database\Model;

class Treinador extends Model
{
    protected ?string $table = 'treinadores';

    protected array $fillable = [
        'usuario_id', 
        'equipe_id', 
        'nome_completo', 
        'cref', 
        'especialidade', 
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
}
