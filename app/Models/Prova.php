<?php

namespace App\Models;

use Core\Database\Model;

class Prova extends Model
{
    protected ?string $table = 'provas';
    protected array $fillable = ['competicao_id', 'categoria_id', 'aparelho', 'descricao', 'max_participantes', 'tipo_calculo', 'num_jurados', 'encerrada'];

    public bool $softDeletes = true;
    
    /**
     * Uma prova tem vários jurados designados.
     */
    public function designacoes(): object
    {
        return $this->hasMany(JuradoDesignacao::class, 'prova_id', 'id');
    }

    /**
     * Uma prova pertence a uma competição.
     */
    public function competicao(): object
    {
        return $this->belongsTo(Competicao::class, 'competicao_id', 'id');
    }

    /**
     * Uma prova é para uma categoria específica.
     */
    public function categoria(): object
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }

    /**
     * Uma prova tem várias inscrições (atletas participando).
     */
    public function inscricoes(): object
    {
        return $this->hasMany(Inscricao::class, 'prova_id', 'id');
    }
}
