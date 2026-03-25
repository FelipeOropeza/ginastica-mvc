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
    <div class="overflow-x-auto md:overflow-visible min-h-[500px]">
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
                    <?php 
                        $total = count($competicoes);
                        foreach ($competicoes as $index => $comp): 
                            include __DIR__ . '/partials/row.php';
                        endforeach;
                    ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


