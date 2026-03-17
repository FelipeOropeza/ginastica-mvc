<?php

namespace App\Database\Migrations;

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateSettingsTable
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->id();
            $table->string('chave', 100)->unique();
            $table->text('valor')->nullable();
            $table->string('descricao')->nullable();
            $table->string('grupo', 50)->nullable(); // ex: 'geral', 'notas', 'sistema'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracoes');
    }
}
