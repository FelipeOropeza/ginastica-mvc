<?php

namespace App\DTOs\Admin;

use Core\Validation\DataTransferObject;
use Core\Attributes\Required;
use Core\Attributes\Email;

use Core\Attributes\Min;

class UserDTO extends DataTransferObject
{
    #[Required('O nome é obrigatório')]
    public string $nome;

    #[Required('O e-mail é obrigatório')]
    #[Email('Informe um e-mail válido')]
    public string $email;

    #[Required('O papel (role) é obrigatório')]
    public int $role_id;

    public int $ativo = 1;

    #[Min(8, 'A senha deve ter no mínimo 8 caracteres')]
    public ?string $senha = null;
}
