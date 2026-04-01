<?php

namespace App\Controllers\Admin;

use App\Services\Admin\AssistantService;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin'])]
class AssistantController
{
    public function __construct(protected AssistantService $assistantService)
    {
    }

    #[Get('/admin/assistente', name: 'admin.assistente.index')]
    public function index()
    {
        return view('admin/assistente/index', [
            'title' => 'Assistente Inteligente'
        ]);
    }

    #[Post('/admin/assistente/perguntar', name: 'admin.assistente.perguntar')]
    public function perguntar()
    {
        $pergunta = request()->input('pergunta');
        
        if (empty(trim($pergunta))) {
            return "<div class='text-red-500 mb-4 p-3 bg-red-50 border border-red-200 rounded'>Por favor, digite uma pergunta.</div>";
        }

        try {
            $resposta = $this->assistantService->processAdminQuery($pergunta);
            
            return view('admin/assistente/partials/resposta', [
                'pergunta' => $pergunta,
                'resposta' => $resposta
            ]);
        } catch (\Exception $e) {
            return "<div class='text-red-500 mb-4 p-3 bg-red-50 border border-red-200 rounded'>Erro ao processar instrução: " . e($e->getMessage()) . "</div>";
        }
    }
}
