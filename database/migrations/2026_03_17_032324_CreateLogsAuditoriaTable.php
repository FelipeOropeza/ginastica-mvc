<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

class CreateLogsAuditoriaTable
{
    public function up(): void
    {
        Schema::create('logs_auditoria', function (Blueprint $table) {
            $table->id();
            $table->integer('usuario_id')->unsigned()->nullable(); // quem fez
            $table->string('acao', 100);         // ex: 'nota.criada', 'competicao.encerrada'
            $table->string('tabela', 100)->nullable(); // tabela afetada
            $table->integer('registro_id')->nullable(); // ID do registro afetado
            $table->text('dados_antes')->nullable(); // JSON dos dados antes
            $table->text('dados_depois')->nullable(); // JSON dos dados depois
            $table->string('ip', 45)->nullable();
            $table->timestamp('feito_em')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_auditoria');
    }
}
