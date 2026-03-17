<?php

namespace App\Models;

use Core\Database\Model;

class Competicao extends Model
{
    protected ?string $table = 'competicoes';
    protected array $fillable = ['nome', 'data_inicio', 'data_fim', 'local', 'descricao', 'status', 'criado_por'];
    
    public bool $softDeletes = true;

    /**
     * Uma competição tem várias provas/aparelhos.
     */
    public function provas(): mixed
    {
        return $this->hasMany(Prova::class, 'competicao_id', 'id');
    }

    /**
     * Uma competição foi criada por um usuário.
     */
    public function criador(): mixed
    {
        return $this->belongsTo(Usuario::class, 'criado_por', 'id');
    }
}
