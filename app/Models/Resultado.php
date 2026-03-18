<?php

namespace App\Models;

use Core\Database\Model;

class Resultado extends Model
{
    protected ?string $table = 'resultados';
    protected array $fillable = [
        'inscricao_id', 'nota_d', 'nota_e', 'penalidade', 
        'nota_final', 'classificacao', 'podio', 'calculado'
    ];

    public function inscricao()
    {
        return $this->belongsTo(Inscricao::class, 'inscricao_id');
    }
}
