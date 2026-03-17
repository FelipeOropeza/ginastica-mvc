<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Atletas Cadastrados</h2>
        <p class="text-sm text-slate-500">Visualize e gerencie os perfis técnicos dos atletas.</p>
    </div>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Atleta</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Equipe</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Categoria</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($atletas)): ?>
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-sm italic">
                            Nenhum atleta cadastrado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($atletas as $atleta): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs uppercase">
                                        <?= strtoupper(substr($atleta->nome_completo ?? 'A', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-700 text-sm leading-tight"><?= e($atleta->nome_completo) ?></p>
                                        <p class="text-[10px] text-slate-400 font-medium tracking-tight">CPF: <?= e($atleta->cpf ?? '--') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-medium text-slate-600">
                                    <?= e($atleta->equipe->nome ?? 'Sem Equipe') ?>
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[10px] font-bold text-slate-500 uppercase">
                                    <?= e($atleta->categoria->nome ?? 'N/A') ?>
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase <?= $atleta->ativo ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' ?>">
                                    <?= $atleta->ativo ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <a href="<?= route('admin.atletas.toggle_status', ['id' => $atleta->id]) ?>" class="w-7 h-7 rounded border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-white <?= $atleta->ativo ? 'hover:text-red-500' : 'hover:text-green-500' ?> transition-all shadow-sm">
                                        <i class="fa-solid <?= $atleta->ativo ? 'fa-user-slash' : 'fa-user-check' ?> text-[10px]"></i>
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
