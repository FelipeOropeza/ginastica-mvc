<?php

namespace App\DTOs\Admin;

use Core\Validation\DataTransferObject;
use Core\Attributes\Required;
use Core\Attributes\Trim;
use Core\Attributes\Unique;

class CompetitionDTO extends DataTransferObject
{
    public ?int $id = null;

    #[Required(message: 'O nome da competição é obrigatório.')]
    #[Trim]
    #[Unique(table: 'competicoes', column: 'nome', ignore: 'id', message: 'Já existe uma competição com este nome.')]
    public string $nome;

    #[Required(message: 'A data de início é obrigatória.')]
    public string $data_inicio;

    #[Required(message: 'A data de término é obrigatória.')]
    public string $data_fim;

    #[Required(message: 'O local é obrigatório.')]
    #[Trim]
    public string $local;

    #[Trim]
    public ?string $descricao = null;
    public string $status = 'rascunho';
}
