<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateNotasTable
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->integer('inscricao_id')->unsigned();  // qual atleta/prova
            $table->integer('jurado_id')->unsigned();     // usuario com role 'jurado'
            // Critério avaliado:
            // nota_d = Dificuldade (painel D)
            // nota_e = Execução (painel E, 0-10)
            // penalidade = Deduções do árbitro superior
            $table->enum('criterio', ['nota_d', 'nota_e', 'penalidade'])->nullable();
            $table->decimal('valor', 5, 3);              // Ex: 9.250, 5.600
            $table->text('observacao')->nullable();
            $table->timestamp('registrado_em')->nullable();
            $table->timestamps();

            $table->foreign('inscricao_id')->references('id')->on('inscricoes');
            $table->foreign('jurado_id')->references('id')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
}
