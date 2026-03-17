<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateCategoriasTable
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            // Ex: Iniciação, Formação, Pré-Equipe, Juvenil I, Juvenil II, Adulto
            $table->string('nome', 100);
            $table->integer('idade_min')->nullable(); // Idade mínima em anos
            $table->integer('idade_max')->nullable(); // Idade máxima em anos
            $table->text('descricao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
}
