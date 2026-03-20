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

    /**
     * Calcula o ranking (classificação) para todos os atletas de uma prova.
     */
    public static function calcularRanking(int $provaId)
    {
        $db = \Core\Database\Connection::getInstance();
        
        $sql = "SELECT r.* FROM resultados r 
                JOIN inscricoes i ON r.inscricao_id = i.id 
                WHERE i.prova_id = :prova_id 
                ORDER BY r.nota_final DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute(['prova_id' => $provaId]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $resultados = $stmt->fetchAll();

        if (empty($resultados)) return;

        $posicao = 1;
        $ultimaNota = null;
        $atrasoEmpate = 0;

        foreach ($resultados as $r) {
            // Lógica de empate/classificação
            if ($ultimaNota !== null && $r->nota_final < $ultimaNota) {
                $posicao += $atrasoEmpate + 1;
                $atrasoEmpate = 0;
            } elseif ($ultimaNota !== null && $r->nota_final == $ultimaNota) {
                $atrasoEmpate++;
            }

            $r->classificacao = $posicao;
            
            // Atribui pódio para o 1º, 2º e 3º lugares reais
            if ($posicao === 1) $r->podio = 1; // ouro
            elseif ($posicao === 2) $r->podio = 2; // prata
            elseif ($posicao === 3) $r->podio = 3; // bronze
            else $r->podio = null;

            $r->save();
            $ultimaNota = $r->nota_final;
        }
    }
}
