<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateProvasTable
{
    public function up(): void
    {
        Schema::create('provas', function (Blueprint $table) {
            $table->id();
            $table->integer('competicao_id')->unsigned();
            $table->integer('categoria_id')->unsigned()->nullable();

            // Aparelho: os 4 da Ginástica Artística Feminina
            $table->enum('aparelho', ['solo', 'salto', 'barras_assimetricas', 'trave']);

            $table->string('descricao', 255)->nullable();
            $table->integer('max_participantes')->nullable();

            // Como a nota será calculada nesta prova
            // media_simples | media_sem_extremos | nota_d_mais_e (regras FIG)
            $table->enum('tipo_calculo', ['media_simples', 'media_sem_extremos', 'nota_d_mais_e'])->nullable();

            $table->integer('num_jurados')->nullable(); // Quantos jurados avaliam esta prova
            $table->boolean('encerrada')->nullable();
            $table->timestamps();

            $table->foreign('competicao_id')->references('id')->on('competicoes');
            $table->foreign('categoria_id')->references('id')->on('categorias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provas');
    }
}
