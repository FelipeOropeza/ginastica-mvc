<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateEquipesTable
{
    public function up(): void
    {
        Schema::create('equipes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('cidade', 100)->nullable();
            $table->string('estado', 50)->nullable();
            $table->string('cores', 100)->nullable();   // ex: "Vermelho e Branco"
            $table->boolean('ativo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipes');
    }
}
