# Agent Guidelines for Ginastica MVC Project

## Project Overview

This is a custom PHP MVC framework project (not Laravel) with:
- PHP 8.5+ required
- Pure PHP views (Twig optional)
- FrankenPHP/Docker ready
- MariaDB/MySQL database
- Redis for caching and queues
- PHPMailer for emails
- Mercure for real-time events

---

## Build/Test/Lint Commands

### Development Server
```bash
# Start development server (PHP built-in)
composer start

# Or with Docker (FrankenPHP + Redis)
docker-compose up -d --build
# Access at http://localhost:8000
```

### Database Commands (Forge CLI)
```bash
# Run migrations
php forge migrate

# Refresh migrations (rollback + migrate)
php forge migrate:refresh

# Create migration
php forge make:migration CreateUsersTable

# Seed database
php forge db:seed [SeederName]
php forge make:seeder MySeeder
```

### Code Generation
```bash
php forge make:controller NomeController
php forge make:model ModelName
php forge make:service NomeService
php forge make:middleware AuthMiddleware
php forge make:dto Admin/UserDTO
php forge make:view secao/nova-view
php forge make:component nome_componente
php forge make:rule CpfValido
php forge make:mutator LimpaCpf
php forge make:job NomeJob
php forge make:command NomeCommand
php forge make:seeder MySeeder
```

### Setup Scaffolds
```bash
php forge setup:auth      # Session-based auth (MVC)
php forge setup:api       # JWT-based API auth
php forge setup:aviso     # Real-time notifications
php forge setup:engine twig  # Switch view engine
```

### Performance
```bash
php forge optimize        # Compile routes to cache
php forge optimize:clear  # Clear route cache
php forge queue:work      # Process background jobs
```

---

## Code Style Guidelines

### PHP File Structure
```php
<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Http\Response;
use App\DTOs\Auth\LoginDTO;

class AuthController
{
    // Constructor injection via DI container
    public function __construct(private AuthService $authService)
    {
    }
}
```

### Naming Conventions

| Element | Convention | Example |
|---------|------------|---------|
| Classes | PascalCase | `AuthController`, `CompetitionService` |
| Methods | camelCase | `getAll()`, `createCompetition()` |
| Variables | camelCase | `$userData`, `$competicoesAtivas` |
| Properties | camelCase | `protected string $table` |
| Constants | SCREAMING_SNAKE | `MAX_RETRIES`, `DEFAULT_STATUS` |
| Tables | snake_case (plural) | `usuarios`, `competicoes`, `jurado_designacoes` |
| Files | PascalCase (classes) | `AuthController.php`, `UserDTO.php` |
| Namespaces | PascalCase | `App\Controllers\Admin` |
| Routes | kebab-case | `/admin/competicoes/criar` |

### Imports
- Use `use` statements for classes
- Group imports: internal app classes first, then core framework, then vendors
- One blank line between groups

```php
use App\Models\Usuario;
use App\DTOs\Auth\LoginDTO;
use Core\Http\Response;
use Core\Attributes\Route\Get;
```

### Type Declarations
- Always use strict types: `declare(strict_types=1);`
- Use typed properties and return types
- Use union types where appropriate (PHP 8+)

```php
public function findById(int $id): Competicao
protected array $fillable = ['nome', 'email', 'senha'];
private ?string $table = null;
```

### PHP 8 Attributes (Annotations)

Use attributes for routing instead of manual route files:

```php
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin,operador'])]
class CompetitionController
{
    #[Get('/admin/competicoes', name: 'admin.competicoes.index')]
    public function index() { }

    #[Post('/admin/competicoes/store', name: 'admin.competicoes.store')]
    public function store(CompetitionDTO $dto) { }
}
```

### Validation with DTOs

Use PHP 8 attributes for validation in DTOs:

```php
use Core\Validation\DataTransferObject;
use Core\Attributes\Required;
use Core\Attributes\Email;
use Core\Attributes\Trim;
use Core\Attributes\Unique;

class CompetitionDTO extends DataTransferObject
{
    public ?int $id = null;

    #[Required(message: 'O nome da competição é obrigatório.')]
    #[Trim]
    #[Unique(table: 'competicoes', column: 'nome', ignore: 'id')]
    public string $nome;

    #[Required(message: 'A data de início é obrigatória.')]
    public string $data_inicio;
}
```

Available validation attributes in `Core\Attributes\`:
- `#[Required]` - Mandatory field
- `#[Email]` - Email validation
- `#[Min(n)]`, `#[Max(n)]` - Length/number limits
- `#[Trim]` - Whitespace trimming
- `#[Unique(table, column, ignore)]` - Database uniqueness
- `#[MatchField('fieldName')]` - Match another field
- `#[IsInt]`, `#[IsFloat]`, `#[IsBool]` - Type casting
- `#[File]`, `#[Image]` - File upload validation
- `#[Hash]` - Hash value

### Controllers Pattern

```php
namespace App\Controllers\Admin;

use App\Services\Admin\CompetitionService;
use App\DTOs\Admin\CompetitionDTO;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;

class CompetitionController
{
    public function __construct(protected CompetitionService $service)
    {
    }

    #[Get('/admin/competicoes', name: 'admin.competicoes.index')]
    public function index()
    {
        $competicoes = $this->service->getAll();
        return view('admin/competicoes/index', [
            'title' => 'Gestão de Competições',
            'competicoes' => $competicoes
        ]);
    }

    #[Post('/admin/competicoes/store')]
    public function store(CompetitionDTO $dto)
    {
        $this->service->create($dto);
        return Response::makeRedirect('/admin/competicoes');
    }
}
```

### Models Pattern

```php
namespace App\Models;

use Core\Database\Model;

class Competicao extends Model
{
    protected ?string $table = 'competicoes';
    protected array $fillable = ['nome', 'data_inicio', 'data_fim', 'local', 'descricao', 'status'];
    protected array $hidden = ['senha'];

    public bool $timestamps = true;
    public bool $softDeletes = false;

    public function provas()
    {
        return $this->hasMany(Prova::class, 'competicao_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
```

Relationship methods available:
- `belongsTo(Class::class, 'foreign_key')`
- `hasMany(Class::class, 'foreign_key')`
- `hasOne(Class::class, 'foreign_key')`

Query builder methods:
```php
$model->all()
$model->find($id)
$model->findOrFail($id)
$model->where('column', '=', 'value')->first()
$model->with('relation')->get()
$model->insert($data)  // Returns inserted ID
$model->update($id, $data)
$model->delete($id)
$model->transaction(fn() => /* ... */)
```

### Services Pattern

Services handle business logic. Keep controllers thin:

```php
namespace App\Services\Admin;

use App\Models\Competicao;
use App\DTOs\Admin\CompetitionDTO;
use Core\Exceptions\ValidationException;

class CompetitionService
{
    public function getAll()
    {
        return (new Competicao())->with('provas')->get();
    }

    public function create(CompetitionDTO $dto): Competicao
    {
        $competicao = new Competicao();
        $competicao->nome = $dto->nome;
        $competicao->data_inicio = $dto->data_inicio;
        $competicao->save();
        return $competicao;
    }
}
```

### Middleware Pattern

```php
namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;

class AuthMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        if (!session()->has('user')) {
            if ($request->isHtmx()) {
                return response()->hxRedirect('/login');
            }
            if ($request->isAjax()) {
                return response()->json(['error' => 'Sessão expirada.'], 401);
            }
            return Response::makeRedirect('/login');
        }
        return $next($request);
    }
}
```

### Views (PHP Templates)

```php
<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="content">
    <h1><?= e($competicao->nome) ?></h1>
    
    <?php foreach ($items as $item): ?>
        <div class="item">
            <p><?= e($item->descricao) ?></p>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($items)): ?>
        <p>Nenhum item encontrado.</p>
    <?php endif; ?>
</div>
```

Key view helpers:
- `e($var)` - HTML escape (always use for user input)
- `old('field')` - Get previous input after validation
- `errors('field')` - Get validation errors
- `csrf_field()` - CSRF token input
- `route('name', ['id' => 1])` - Generate URLs

### Error Handling

```php
// Validation errors - throws ValidationException automatically via DTO
use Core\Exceptions\ValidationException;

// Manual validation failure
fail_validation(['email' => 'Credenciais inválidas.']);

// HTTP errors
abort(404);  // Throws HttpException
abort(403, 'Sem permissão');

// Try-catch pattern
try {
    $result = $this->service->update($id, $dto);
} catch (ValidationException $e) {
    if (request()->isHtmx()) {
        return new Response("<span class='error'>{$e->errors['field'][0]}</span>");
    }
    throw $e;
}
```

### Directory Structure

```
app/
  Controllers/
    Admin/          # Admin panel controllers
    Atleta/         # Athlete controllers
    Juiz/           # Judge controllers
    AuthController.php
  DTOs/
    Auth/           # Auth DTOs
    Admin/          # Admin DTOs
  Middleware/
    AuthMiddleware.php
    RoleMiddleware.php
  Models/
    Usuario.php
    Competicao.php
    Atleta.php
  Services/
    AuthService.php
    Admin/          # Admin services
  Views/
    admin/
    atleta/
    juiz/
    auth/
    layouts/
    partials/
    components/
  Providers/
    AppServiceProvider.php
    MercureServiceProvider.php
  Rules/           # Custom validation rules
  Mutators/        # Custom mutators
core/
  Attributes/      # Validation attributes
  Auth/
  Cache/
  Console/
  Contracts/
  Database/
  Exceptions/
  Http/
  Mail/
  Queue/
  Routing/
  Support/
  Validation/
config/
  app.php          # Providers and paths
  database.php     # DB connections
  middleware.php   # Middleware config
routes/
  web.php          # Main routes
  auth.php         # Auth routes
database/
  migrations/
  seeders/
public/
  index.php        # Entry point
```

### Dependency Injection

The framework uses autowiring via reflection. Constructor injection is preferred:

```php
// Auto-resolved
public function __construct(protected CompetitionService $service)
{
}

// Manual binding in ServiceProvider
$this->app->singleton(SomeInterface::class, SomeImplementation::class);
```

### Global Helpers

Available throughout the application:
- `app()` - Container/DI access
- `request()` - Current HTTP request
- `response()` - Response factory
- `view()` - Render view
- `session()` - Session manager
- `redirect()` - Redirect response
- `validate($dto)` - Validate DTO
- `fail_validation()` - Manual validation failure
- `errors()` - Get validation errors
- `old()` - Get old input
- `route()` - Generate URL
- `abort()` - Throw HTTP exception
- `logger()` - Logger instance
- `mailer()` - Mailer instance
- `broadcast()` - Mercure broadcast
- `e()` - HTML escape
- `storage_url()` - Storage file URL
- `env()` - Environment variable

---

## Testing

This project does not currently have a test suite configured. When adding tests:
- Use PHPUnit
- Place tests in `tests/` directory
- Follow PSR-12 standards
- Mock database interactions where possible

---

## Database Conventions

- Use migrations for all schema changes: `php forge make:migration Name`
- Use snake_case for all column names
- Always define `$fillable` array for mass assignment protection
- Use `$hidden` for sensitive fields (passwords, tokens)
- Enable `$timestamps = true` for created_at/updated_at management
- Use soft deletes for important records: `$softDeletes = true`

---

## Security Guidelines

- Always escape user input in views with `e()`
- Use DTOs with validation attributes for all user input
- Never trust user data without validation
- Hash passwords with `password_hash()` / `password_verify()`
- Use CSRF tokens in all forms: `csrf_field()`
- Sanitize file uploads using the `#[File]` or `#[Image]` validation attributes
- Keep secrets in `.env` (never commit it)
