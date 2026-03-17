<?php

namespace App\DTOs\Admin;

use Core\Validation\DataTransferObject;
use Core\Attributes\Required;
use Core\Attributes\IsInt;
use Core\Attributes\Trim;
use Core\Attributes\Min;

class ProvaDTO extends DataTransferObject
{
    #[Required(message: 'A categoria é obrigatória.')]
    #[IsInt(message: 'ID da categoria inválido.')]
    public int $categoria_id;

    #[Required(message: 'O aparelho é obrigatório.')]
    #[Trim]
    public string $aparelho;

    #[Required(message: 'O tipo de cálculo é obrigatório.')]
    public string $tipo_calculo = 'media_simples';

    #[Required(message: 'O número de jurados é obrigatório.')]
    #[IsInt(message: 'O número de jurados deve ser um número inteiro.')]
    #[Min(1, 'Mínimo de 1 jurado.')]
    public int $num_jurados = 3;
}
