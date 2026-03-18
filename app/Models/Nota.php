<?php

namespace App\Models;

use Core\Database\Model;

class Nota extends Model
{
    protected ?string $table = 'notas';
    protected array $fillable = ['inscricao_id', 'jurado_id', 'criterio', 'valor', 'observacao', 'registrado_em'];

    public function inscricao()
    {
        return $this->belongsTo(Inscricao::class, 'inscricao_id');
    }

    public function jurado()
    {
        return $this->belongsTo(Usuario::class, 'jurado_id');
    }
}
