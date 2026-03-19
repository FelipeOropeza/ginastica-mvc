<?php

namespace App\DTOs\Auth;

use Core\Validation\DataTransferObject;
use Core\Attributes\Required;
use Core\Attributes\Email;
use Core\Attributes\Min;
use Core\Attributes\Unique;
use Core\Attributes\Hash;
use Core\Attributes\MatchField;

class RegisterDTO extends DataTransferObject
{
    #[Required(message: 'O nome é obrigatório.')]
    public string $nome;

    #[Required(message: 'O e-mail é obrigatório.')]
    #[Email(message: 'Forneça um e-mail válido.')]
    #[Unique(table: 'usuarios', column: 'email', message: 'Este e-mail já está em uso.')]
    public string $email;

    #[Required(message: 'A senha é obrigatória.')]
    #[Min(8, message: 'A senha deve ter no mínimo 8 caracteres.')]
    #[Hash]
    public string $senha;

    #[Required(message: 'A confirmação de senha é obrigatória.')]
    #[MatchField('senha', message: 'As senhas não coincidem.')]
    public string $senha_confirmacao;
}
