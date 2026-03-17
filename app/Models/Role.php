<?php

namespace App\Models;

use Core\Database\Model;

class Role extends Model
{
    protected ?string $table = 'roles';
    protected array $fillable = ['nome', 'descricao'];

    /**
     * Relacionamento com os usuários que possuem este papel.
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'role_id');
    }
}
