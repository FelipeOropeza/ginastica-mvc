<?php

namespace App\Models;

use Core\Database\Model;

class Prova extends Model
{
    protected ?string $table = 'provas';
    protected array $fillable = ['competicao_id', 'categoria_id', 'aparelho', 'descricao', 'max_participantes', 'tipo_calculo', 'num_jurados', 'encerrada'];

    /**
     * Uma prova pertence a uma competição.
     */
    public function competicao(): mixed
    {
        return $this->belongsTo(Competicao::class, 'competicao_id', 'id');
    }

    /**
     * Uma prova é para uma categoria específica.
     */
    public function categoria(): mixed
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }
}
