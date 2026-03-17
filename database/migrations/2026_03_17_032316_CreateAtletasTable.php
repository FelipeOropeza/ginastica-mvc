<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateAtletasTable
{
    public function up(): void
    {
        Schema::create('atletas', function (Blueprint $table) {
            $table->id();
            $table->integer('usuario_id')->unsigned()->nullable(); // FK para usuarios (login)
            $table->integer('equipe_id')->unsigned()->nullable();   // FK para equipes
            $table->integer('categoria_id')->unsigned()->nullable(); // FK para categorias
            $table->string('nome_completo', 150);
            $table->date('data_nascimento');
            $table->string('cpf', 14)->unique()->nullable();
            $table->string('numero_registro', 50)->nullable(); // Registro federação
            $table->boolean('ativo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('equipe_id')->references('id')->on('equipes');
            $table->foreign('categoria_id')->references('id')->on('categorias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atletas');
    }
}
