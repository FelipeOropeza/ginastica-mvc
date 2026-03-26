<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateResultadosTable
{
    public function up(): void
    {
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->integer('inscricao_id')->unsigned(); // atleta + prova
            // Notas consolidadas após avaliação:
            $table->decimal('nota_d', 5, 3)->nullable();         // Nota dificuldade
            $table->decimal('nota_e', 5, 3)->nullable();         // Nota execução (média E)
            $table->decimal('penalidade', 5, 3)->nullable();     // Deduções totais
            $table->decimal('nota_final', 5, 3)->nullable();     // D + E - Penalidade
            // Classificação dentro da prova
            $table->integer('classificacao')->nullable();
            // Flag de pódio: 1=ouro, 2=prata, 3=bronze, null=sem pódio
            $table->integer('podio')->nullable();
            $table->boolean('calculado')->default(false);            // já foi processado?
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('inscricao_id')->references('id')->on('inscricoes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
}
