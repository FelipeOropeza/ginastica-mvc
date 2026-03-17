<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateRolesTable
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50)->unique();   // admin | operador | jurado
            $table->string('descricao', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
}
