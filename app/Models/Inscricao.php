<?php

namespace App\Models;

use Core\Database\Model;

class Inscricao extends Model
{
    protected ?string $table = 'inscricoes';
    protected array $fillable = ['atleta_id', 'competicao_id', 'prova_id', 'ordem_apresentacao', 'status', 'inscrito_em'];
}
