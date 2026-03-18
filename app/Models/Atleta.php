<?php

namespace App\Models;

use Core\Database\Model;

class Atleta extends Model
{
    protected ?string $table = 'atletas';
    protected array $fillable = [
        'nome_completo', 
        'data_nascimento', 
        'cpf', 
        'numero_registro', 
        'usuario_id', 
        'equipe_id', 
        'categoria_id', 
        'ativo'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function equipe()
    {
        return $this->belongsTo(Equipe::class, 'equipe_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function inscricoes()
    {
        return $this->hasMany(Inscricao::class, 'atleta_id');
    }

    /**
     * Retorna a melhor pontuação do atleta em qualquer prova.
     */
    public function melhorNota()
    {
        return (new Resultado())
            ->select('resultados.*')
            ->join('inscricoes', 'inscricoes.id = resultados.inscricao_id')
            ->where('inscricoes.atleta_id', '=', $this->id)
            ->orderBy('resultados.nota_final', 'DESC')
            ->first();
    }
}
