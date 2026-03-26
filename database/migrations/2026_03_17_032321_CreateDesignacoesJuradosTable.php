<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateDesignacoesJuradosTable
{
    public function up(): void
    {
        Schema::create('designacoes_jurados', function (Blueprint $table) {
            $table->id();
            $table->integer('prova_id')->unsigned();
            $table->integer('usuario_id')->unsigned(); // usuário com role 'jurado'
            // Critério que este jurado avalia nesta prova:
            // nota_d = Dificuldade | nota_e = Execução | arbitro_superior | geral
            $table->enum('criterio', ['nota_d', 'nota_e', 'arbitro_superior', 'geral', 'penalidade'])->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('prova_id')->references('id')->on('provas');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designacoes_jurados');
    }
}
