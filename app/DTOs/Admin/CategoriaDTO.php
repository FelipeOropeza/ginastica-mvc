<?php

namespace App\DTOs\Admin;

use Core\Validation\DataTransferObject;
use Core\Attributes\Required;
use Core\Attributes\Trim;

class CategoriaDTO extends DataTransferObject
{
    #[Required(message: 'O nome da categoria é obrigatório.')]
    #[Trim]
    public string $nome;

    public ?int $idade_min = null;
    public ?int $idade_max = null;
    
    #[Trim]
    public ?string $descricao = null;
}
