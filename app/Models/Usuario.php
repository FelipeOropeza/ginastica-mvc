<?php

namespace App\Models;

use Core\Database\Model;

class Usuario extends Model
{
    protected ?string $table = 'usuarios';
    protected array $fillable = ['nome', 'email', 'senha', 'role_id', 'ativo'];
    protected array $hidden = ['senha'];

    /**
     * Relacionamento com o papel (Role) do usuário.
     */
    public bool $softDeletes = true;

    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }

    /**
     * Relacionamento com o perfil de atleta (se houver).
     */
    public function atleta()
    {
        return $this->hasOne(\App\Models\Atleta::class, 'usuario_id');
    }

    /**
     * Relacionamento com o perfil de treinador (se houver).
     */
    public function treinador()
    {
        return $this->hasOne(\App\Models\Treinador::class, 'usuario_id');
    }
}
