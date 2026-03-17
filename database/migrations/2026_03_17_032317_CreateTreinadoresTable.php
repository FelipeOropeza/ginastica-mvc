<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateTreinadoresTable
{
    public function up(): void
    {
        Schema::create('treinadores', function (Blueprint $table) {
            $table->id();
            $table->integer('usuario_id')->unsigned()->nullable(); // FK para usuarios (login)
            $table->integer('equipe_id')->unsigned()->nullable();   // FK para equipes
            $table->string('nome_completo', 150);
            $table->string('cref', 30)->nullable(); // Registro no Conselho de Educação Física
            $table->string('especialidade', 100)->nullable(); // Ex: Ginástica Artística Feminina
            $table->boolean('ativo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('equipe_id')->references('id')->on('equipes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treinadores');
    }
}
