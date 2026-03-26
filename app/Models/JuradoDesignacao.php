<?php

namespace App\Models;

use Core\Database\Model;

class JuradoDesignacao extends Model
{
    protected ?string $table = 'designacoes_jurados';
    protected array $fillable = ['prova_id', 'usuario_id', 'criterio'];

    /**
     * A designação pertence a uma prova.
     */
    public bool $softDeletes = true;

    public function prova(): object
    {
        return $this->belongsTo(Prova::class, 'prova_id', 'id');
    }

    /**
     * A designação pertence a um usuário (jurado).
     */
    public function jurado(): object
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
    }
}
