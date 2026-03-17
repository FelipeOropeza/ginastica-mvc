<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Gestão de Categorias</h2>
        <p class="text-sm text-slate-500">Defina os níveis de competição e faixas etárias.</p>
    </div>
    <a href="<?= route('admin.categorias.create') ?>" class="btn btn-primary gap-2 w-fit">
        <i class="fa-solid fa-plus text-xs"></i> Nova Categoria
    </a>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nome da Categoria</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Faixa Etária</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Descrição</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($categorias)): ?>
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-sm italic">
                            Nenhuma categoria cadastrada.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categorias as $cat): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4 font-bold text-slate-700 text-sm"><?= e($cat->nome) ?></td>
                            <td class="px-5 py-4 text-xs text-slate-500 font-medium">
                                <?= $cat->idade_min ?? '0' ?> a <?= $cat->idade_max ?? '∞' ?> anos
                            </td>
                            <td class="px-5 py-4 text-[10px] text-slate-400 max-w-xs truncate"><?= e($cat->descricao ?? '--') ?></td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <a href="<?= route('admin.categorias.edit', ['id' => $cat->id]) ?>" class="w-7 h-7 rounded border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-white hover:text-amber-600 transition-all shadow-sm">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
