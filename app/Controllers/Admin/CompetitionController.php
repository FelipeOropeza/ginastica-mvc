<?php

namespace App\Controllers\Admin;

use App\Services\Admin\CompetitionService;
use App\DTOs\Admin\CompetitionDTO;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin,operador'])]
class CompetitionController
{
    protected CompetitionService $service;

    public function __construct(CompetitionService $service)
    {
        $this->service = $service;
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

    #[Get('/admin/competicoes/criar', name: 'admin.competicoes.create')]
    public function create()
    {
        return view('admin/competicoes/form', [
            'title' => 'Nova Competição'
        ]);
    }

    #[Post('/admin/competicoes/store', name: 'admin.competicoes.store')]
    public function store(CompetitionDTO $dto)
    {
        $this->service->create($dto);

        return Response::makeRedirect('/admin/competicoes');
    }

    #[Get('/admin/competicoes/{id}/editar', name: 'admin.competicoes.edit')]
    public function edit(int $id)
    {
        $competicao = $this->service->findById($id);

        return view('admin/competicoes/form', [
            'title' => 'Editar Competição',
            'competicao' => $competicao
        ]);
    }

    #[Post('/admin/competicoes/{id}/update', name: 'admin.competicoes.update')]
    public function update(int $id, CompetitionDTO $dto)
    {
        $this->service->update($id, $dto);

        return Response::makeRedirect('/admin/competicoes');
    }

    #[Post('/admin/competicoes/{id}/status', name: 'admin.competicoes.status')]
    public function status(int $id)
    {
        $status = request()->get('status');
        
        try {
            $competicao = $this->service->updateStatus($id, $status);
        } catch (\Core\Exceptions\ValidationException $e) {
            if (request()->isHtmx()) {
                $error = array_shift($e->errors);
                if (is_array($error)) $error = array_shift($error);
                
                // Retornamos o status original (re-renderizamos o badge antigo)
                // ou apenas enviamos um trigger para o Toast
                $competicaoOriginal = $this->service->findById($id);
                
                header('HX-Trigger: {"showAlert": {"type": "error", "message": "' . $error . '"}}');
                
                // Retornamos o badge original para "resetar" o estado visual no htmx
                $statusClasses = [
                    'rascunho' => 'bg-slate-100 text-slate-500 border-slate-200',
                    'aberta' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'em_andamento' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'encerrada' => 'bg-rose-50 text-rose-700 border-rose-200',
                ];
                $statusLabels = [
                    'rascunho' => 'Rascunho',
                    'aberta' => 'Inscrições Abertas',
                    'em_andamento' => 'Ativa',
                    'encerrada' => 'Finalizada',
                ];
                $classe = $statusClasses[$competicaoOriginal->status] ?? 'bg-slate-100 text-slate-600';
                $label = $statusLabels[$competicaoOriginal->status] ?? $competicaoOriginal->status;

                return new Response("<button @click='open = !open; \$event.stopPropagation()' id='status-badge-{$id}' 
                    class='px-2 py-1 rounded-lg border text-[9px] uppercase tracking-tighter font-black transition-all hover:brightness-95 flex items-center gap-1.5 shadow-sm {$classe}'>
                    <span class='w-1.5 h-1.5 rounded-full bg-current opacity-50'></span>
                    {$label}
                    <i class='fa-solid fa-chevron-down opacity-30'></i>
                </button>");
            }
            throw $e;
        }

        if (request()->isHtmx()) {
            return view('admin/competicoes/partials/row', [
                'comp' => $this->service->findById($id)
            ]);
        }

        return Response::makeRedirect('/admin/competicoes');
    }

    #[Post('/admin/competicoes/{id}/deletar', name: 'admin.competicoes.delete')]
    public function delete(int $id)
    {
        $this->service->delete($id);

        if (request()->isHtmx()) {
            return new Response('');
        }

        return Response::makeRedirect('/admin/competicoes');
    }
}
