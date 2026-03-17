<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

/**
 * Atualiza a tabela 'usuarios' gerada pelo setup:auth,
 * adicionando role_id, data_nascimento e outros campos do GymPodium.
 */
class AddRoleToUsuariosTable
{
    public function up(): void
    {
        // Recria a tabela com todos os campos necessários para o GymPodium
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('email', 150)->unique();
            $table->string('senha');
            $table->integer('role_id')->unsigned();
            $table->date('data_nascimento')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->boolean('ativo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
}
