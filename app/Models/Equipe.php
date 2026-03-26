<?php

namespace App\Models;

use Core\Database\Model;

class Equipe extends Model
{
    protected ?string $table = 'equipes';
    protected array $fillable = [
        'nome',
        'cidade',
        'estado',
        'cores',
        'ativo'
    ];

    public bool $softDeletes = true;

    public function atletas()
    {
        return $this->hasMany(Atleta::class, 'equipe_id');
    }

    /**
     * Relacionamento com os treinadores da equipe.
     */
    public function treinadores()
    {
        return $this->hasMany(Treinador::class, 'equipe_id');
    }
}
