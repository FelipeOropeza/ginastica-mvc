<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateInscricoesTable
{
    public function up(): void
    {
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->id();
            $table->integer('atleta_id')->unsigned();
            $table->integer('competicao_id')->unsigned();
            $table->integer('prova_id')->unsigned();
            // Ordem de apresentação do atleta dentro da prova
            $table->integer('ordem_apresentacao')->nullable();
            // Status: pendente | confirmada | desclassificada | retirada
            $table->enum('status', ['pendente', 'confirmada', 'desclassificada', 'retirada'])->nullable();
            $table->timestamp('inscrito_em')->nullable();
            $table->timestamps();

            $table->foreign('atleta_id')->references('id')->on('atletas');
            $table->foreign('competicao_id')->references('id')->on('competicoes');
            $table->foreign('prova_id')->references('id')->on('provas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricoes');
    }
}
