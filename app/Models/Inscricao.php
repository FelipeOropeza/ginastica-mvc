<?php

namespace App\Models;

use Core\Database\Model;

class Inscricao extends Model
{
    protected ?string $table = 'inscricoes';
    protected array $fillable = ['atleta_id', 'competicao_id', 'prova_id', 'ordem_apresentacao', 'reaberta', 'status', 'inscrito_em'];

    public bool $softDeletes = true;

    public function atleta()
    {
        return $this->belongsTo(Atleta::class, 'atleta_id');
    }

    public function competicao()
    {
        return $this->belongsTo(Competicao::class, 'competicao_id');
    }

    public function prova()
    {
        return $this->belongsTo(Prova::class, 'prova_id');
    }

    public function resultado()
    {
        return $this->hasOne(Resultado::class, 'inscricao_id');
    }

    /**
     * Notas individuais dadas por jurados para esta inscrição.
     */
    public function notas()
    {
        return $this->hasMany(Nota::class, 'inscricao_id');
    }

    /**
     * Relação auxiliar para carregar a nota de um jurado específico.
     * Usada no controller via ->with(['notaPorJurado' => function($db) { ... }])
     */
    public function notaPorJurado()
    {
        return $this->hasOne(Nota::class, 'inscricao_id');
    }
}
