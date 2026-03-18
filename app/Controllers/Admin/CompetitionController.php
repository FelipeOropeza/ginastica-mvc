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
                return new Response("<span class='text-[9px] text-red-600 font-bold uppercase block max-w-[150px] leading-tight'>{$error}</span>", 200);
                // Usamos 200 pro HTMX renderizar, mas com cor de erro.
            }
            throw $e;
        }

        if (request()->isHtmx()) {
             // Retornamos apenas o pedaço do status ou a linha inteira para atualizar no front
             // Aqui vou retornar a view parcial do status se eu tivesse uma, mas vou retornar o label formatado
             $statusClasses = [
                'rascunho' => 'bg-slate-100 text-slate-600',
                'aberta' => 'bg-green-50 text-green-700 border border-green-200',
                'em_andamento' => 'bg-blue-50 text-blue-700 border border-blue-200',
                'encerrada' => 'bg-red-50 text-red-700 border border-red-200',
            ];
            $statusLabels = [
                'rascunho' => 'Rascunho',
                'aberta' => 'Aberta',
                'em_andamento' => 'Ativa',
                'encerrada' => 'Finalizada',
            ];
            $classe = $statusClasses[$status] ?? 'bg-slate-100 text-slate-600';
            $label = $statusLabels[$status] ?? $status;

            return new Response("<button @click='open = !open; \$event.stopPropagation()' id='status-badge-{$id}' class='px-2 py-0.5 rounded text-[9px] uppercase tracking-tighter font-bold transition-all hover:brightness-95 {$classe}'>
                {$label}
                <i class='fa-solid fa-chevron-down ml-1 opacity-50'></i>
            </button>");
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
