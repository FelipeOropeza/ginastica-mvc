<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateCompeticoesTable
{
    public function up(): void
    {
        Schema::create('competicoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->string('local', 255)->nullable();
            $table->text('descricao')->nullable();
            // Status: rascunho | aberta | em_andamento | encerrada
            $table->enum('status', ['rascunho', 'aberta', 'em_andamento', 'encerrada'])->nullable();
            $table->integer('criado_por')->unsigned()->nullable(); // FK usuario admin
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('criado_por')->references('id')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competicoes');
    }
}
