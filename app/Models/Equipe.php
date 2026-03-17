<?php

namespace App\Models;

use Core\Database\Model;

class Equipe extends Model
{
    protected ?string $table = 'equipes';
    protected array $fillable = [
        'nome',
        'sigla',
        'pais',
        'ativo'
    ];

    public function atletas()
    {
        return $this->hasMany(Atleta::class, 'equipe_id');
    }
}
