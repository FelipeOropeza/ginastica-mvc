<?php 
    $total = $total ?? 1;
    $index = $index ?? 0;
    $isLastRows = ($total > 3 && $index >= $total - 2);
?>
<tr id="comp-<?= $comp->id ?>" class="hover:bg-slate-50 transition-colors animate-in fade-in duration-300">
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
        <div class="relative inline-block" x-data="{ open: false, id: <?= $comp->id ?> }" @close-menus.window="if($event.detail.id !== id) open = false">
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
            <button @click.stop="open = !open; if(open) $dispatch('close-menus', { id: id })" id="status-badge-<?= $comp->id ?>" 
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
                    class="absolute right-0 <?= $isLastRows ? 'bottom-full mb-3' : 'top-full mt-3' ?> w-52 bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 z-[100] py-2 overflow-hidden border border-slate-100" 
                    style="display: none;">
                <div class="px-4 py-2 mb-1 border-b border-slate-50">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Gerenciar Status</p>
                </div>
                <?php foreach($statusLabels as $val => $label): ?>
                    <button 
                        hx-post="<?= route('admin.competicoes.status', ['id' => $comp->id]) ?>"
                        hx-vals='{"status": "<?= $val ?>"}'
                        hx-headers='{"X-CSRF-TOKEN": "<?= csrf_token() ?>"}'
                        hx-target="#comp-<?= $comp->id ?>"
                        hx-swap="outerHTML"
                        @click="open = false"
                        class="w-full text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest transition-all flex items-center justify-between <?= $comp->status === $val ? 'text-primary-600 bg-primary-50/50' : 'text-slate-600 hover:bg-slate-50 hover:text-primary-600' ?>"
                    >
                        <span><?= $label ?></span>
                        <?php if($comp->status === $val): ?>
                            <i class="fa-solid fa-check text-[10px]"></i>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </td>
    <td class="px-5 py-4 text-right">
        <div class="flex justify-end gap-1.5 items-center">
            <?php if ($comp->status === 'em_andamento'): ?>
                <a href="<?= route('publico.live', ['id' => $comp->id]) ?>" target="_blank" title="Painel Ao Vivo" 
                    class="w-7 h-7 rounded-lg bg-emerald-500 text-white flex items-center justify-center hover:bg-emerald-600 hover:scale-110 transition-all shadow-md shadow-emerald-500/20 animate-pulse">
                    <i class="fa-solid fa-tower-broadcast text-[10px]"></i>
                </a>
            <?php endif; ?>
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

