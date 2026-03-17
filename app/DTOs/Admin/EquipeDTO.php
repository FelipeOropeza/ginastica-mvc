<?php

namespace App\DTOs\Admin;

use Core\Validation\DataTransferObject;
use Core\Attributes\Required;
use Core\Attributes\Trim;

class EquipeDTO extends DataTransferObject
{
    #[Required(message: 'O nome da equipe é obrigatório.')]
    #[Trim]
    public string $nome;

    public ?string $cidade = null;
    public ?string $estado = null;
    public ?string $cores = null;
    public int $ativo = 1;
}
