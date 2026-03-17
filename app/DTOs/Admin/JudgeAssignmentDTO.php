<?php

namespace App\DTOs\Admin;

use Core\Validation\DataTransferObject;
use Core\Attributes\Required;

class JudgeAssignmentDTO extends DataTransferObject
{
    #[Required('Selecione um jurado')]
    public int $usuario_id;

    #[Required('Selecione o critério de avaliação')]
    public string $criterio;
}
