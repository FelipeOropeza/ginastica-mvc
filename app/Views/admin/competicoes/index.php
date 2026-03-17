<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Calendário de Competições</h2>
        <p class="text-sm text-slate-500">Gerencie os eventos e torneios da Academia de Barueri.</p>
    </div>
    <a href="<?= route('admin.competicoes.create') ?>" class="btn btn-primary gap-2 w-fit">
        <i class="fa-solid fa-plus text-xs"></i> Nova Competição
    </a>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Competição</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Período</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Local</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($competicoes)): ?>
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <i class="fa-solid fa-calendar-xmark text-3xl text-slate-200 mb-3 block"></i>
                            <p class="text-slate-500 text-sm font-medium">Nenhuma competição cadastrada.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($competicoes as $comp): ?>
                        <tr id="comp-<?= $comp->id ?>" class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-slate-100 text-slate-500 rounded flex items-center justify-center text-sm shrink-0">
                                        <i class="fa-solid fa-medal"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800 text-sm truncate"><?= e($comp->nome) ?></p>
                                        <p class="text-[10px] text-slate-400 uppercase truncate"><?= $comp->status ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-xs font-medium text-slate-600">
                                    <span class="block"><?= date('d/m/y', strtotime($comp->data_inicio)) ?></span>
                                    <span class="text-[10px] text-slate-400 lowercase italic">até <?= date('d/m/y', strtotime($comp->data_fim)) ?></span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-500">
                                <?= e($comp->local) ?>
                            </td>
                            <td class="px-5 py-4">
                                <?php 
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
                                    $classe = $statusClasses[$comp->status] ?? 'bg-slate-100 text-slate-600';
                                ?>
                                <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-tighter font-bold <?= $classe ?>">
                                    <?= $statusLabels[$comp->status] ?? e($comp->status) ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <a href="<?= route('admin.provas.index', ['id' => $comp->id]) ?>" title="Provas" class="w-7 h-7 rounded border border-slate-200 text-slate-500 flex items-center justify-center hover:bg-slate-50 hover:text-primary-600 transition-all shadow-sm">
                                        <i class="fa-solid fa-list-check text-[10px]"></i>
                                    </a>
                                    <a href="<?= route('admin.competicoes.edit', ['id' => $comp->id]) ?>" title="Editar" class="w-7 h-7 rounded border border-slate-200 text-slate-500 flex items-center justify-center hover:bg-slate-50 hover:text-amber-600 transition-all shadow-sm">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </a>
                                    <button 
                                        hx-post="<?= route('admin.competicoes.delete', ['id' => $comp->id]) ?>"
                                        hx-headers='{"X-CSRF-TOKEN": "<?= csrf_token() ?>"}'
                                        hx-confirm="Excluir competição?"
                                        hx-target="#comp-<?= $comp->id ?>"
                                        hx-swap="outerHTML"
                                        class="w-7 h-7 rounded border border-slate-200 text-slate-500 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all shadow-sm"
                                    >
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
