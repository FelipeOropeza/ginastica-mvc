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
    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }
}
