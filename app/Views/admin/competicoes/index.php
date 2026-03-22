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
    <div class="overflow-x-auto md:overflow-visible min-h-[400px]">
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
                                <div class="relative inline-block" x-data="{ open: false }">
                                    <?php 
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
                                        $classe = $statusClasses[$comp->status] ?? 'bg-slate-100 text-slate-600';
                                    ?>
                                    <button @click="open = !open; $event.stopPropagation()" id="status-badge-<?= $comp->id ?>" 
                                            class="px-2 py-1 rounded-lg border text-[9px] uppercase tracking-tighter font-black transition-all hover:brightness-95 flex items-center gap-1.5 shadow-sm <?= $classe ?>">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>
                                        <?= $statusLabels[$comp->status] ?? e($comp->status) ?>
                                        <i class="fa-solid fa-chevron-down opacity-30"></i>
                                    </button>

                                    <!-- Status Dropdown -->
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         @click.away="open = false" 
                                         class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 z-[100] py-2 overflow-hidden" 
                                         style="display: none;">
                                        <div class="px-3 pb-2 mb-1 border-b border-slate-50">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Alterar Status</p>
                                        </div>
                                        <?php foreach($statusLabels as $val => $label): ?>
                                            <button 
                                                hx-post="<?= route('admin.competicoes.status', ['id' => $comp->id]) ?>"
                                                hx-vals='{"status": "<?= $val ?>"}'
                                                hx-headers='{"X-CSRF-TOKEN": "<?= csrf_token() ?>"}'
                                                hx-target="#status-badge-<?= $comp->id ?>"
                                                hx-swap="outerHTML"
                                                @click="open = false"
                                                class="w-full text-left px-4 py-2 text-[10px] font-bold uppercase tracking-widest transition-all flex items-center justify-between <?= $comp->status === $val ? 'text-primary-600 bg-primary-50/50' : 'text-slate-600 hover:bg-slate-50 hover:text-primary-600' ?>"
                                            >
                                                <?= $label ?>
                                                <?php if($comp->status === $val): ?>
                                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                                <?php endif; ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
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
