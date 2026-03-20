<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-outfit font-black text-slate-800 tracking-tight">Atletas e Ginastas</h2>
        <p class="text-sm text-slate-500 font-medium">Gestão técnica de categorias, equipes e status de acesso.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-100 text-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total</p>
            <p class="text-xl font-bold text-slate-700 leading-none"><?= count($atletas) ?></p>
        </div>
        <div class="bg-primary-50 px-4 py-2 rounded-xl shadow-sm border border-primary-100 text-center">
            <p class="text-[10px] font-black text-primary-400 uppercase tracking-widest leading-none mb-1">Ativos</p>
            <p class="text-xl font-bold text-primary-600 leading-none"><?= count(array_filter($atletas, fn($a) => $a->ativo)) ?></p>
        </div>
    </div>
</div>

<div class="card overflow-hidden border-none shadow-xl bg-white/80 backdrop-blur-md">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Informações do Ginasta</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Equipe Atribuída</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Categoria Técnica</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Situação</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] text-right">Ações Rápidas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($atletas)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-200">
                                <i class="fa-solid fa-user-ghost text-2xl"></i>
                            </div>
                            <p class="text-slate-400 text-sm font-bold uppercase tracking-widest">Nenhum atleta base encontrado</p>
                            <p class="text-xs text-slate-400 italic">Cadastre usuários como atletas para vê-los aqui.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($atletas as $atleta): ?>
                        <tr class="hover:bg-primary-50/30 transition-all group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 text-slate-500 flex items-center justify-center font-black text-sm uppercase shadow-inner group-hover:from-primary-500 group-hover:to-primary-600 group-hover:text-white transition-all duration-300">
                                        <?= strtoupper(substr($atleta->nome_completo ?? $atleta->usuario->nome ?? 'A', 0, 1)) ?>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-black text-slate-800 text-sm leading-none mb-1 group-hover:text-primary-700 transition-colors"><?= e($atleta->nome_completo ?? $atleta->usuario->nome) ?></p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">CP: <?= e($atleta->cpf ?? 'PENDENTE') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <?php if($atleta->equipe): ?>
                                        <span class="text-xs font-bold text-slate-700 leading-none mb-1"><?= e($atleta->equipe->nome) ?></span>
                                        <span class="text-[9px] text-slate-400 uppercase tracking-widest font-black">Escala Oficial</span>
                                    <?php else: ?>
                                        <span class="text-[10px] text-amber-500 font-bold uppercase italic"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Não Vinculado</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <?php if($atleta->categoria): ?>
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-[9px] font-black text-slate-500 uppercase tracking-wider border border-slate-200 group-hover:bg-white group-hover:border-primary-200 transition-all">
                                        <?= e($atleta->categoria->nome) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 rounded-lg bg-red-50 text-[9px] font-black text-red-400 uppercase tracking-wider border border-red-100">
                                        PENDENTE
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-5">
                                <a href="<?= route('admin.atletas.toggle_status', ['id' => $atleta->id]) ?>" 
                                   class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border transition-all 
                                   <?= $atleta->ativo ? 'bg-green-50 text-green-600 border-green-100 hover:bg-red-50 hover:text-red-500 hover:border-red-100' : 'bg-red-50 text-red-500 border-red-100 hover:bg-green-50 hover:text-green-600 hover:border-green-100' ?>">
                                    <span class="w-1.5 h-1.5 rounded-full <?= $atleta->ativo ? 'bg-green-500' : 'bg-red-500' ?>"></span>
                                    <?= $atleta->ativo ? 'Ativo' : 'Inativo' ?>
                                </a>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <button hx-get="<?= route('admin.atletas.edit', ['id' => $atleta->id]) ?>" 
                                            hx-target="#modal-container" 
                                            hx-swap="innerHTML"
                                            class="w-9 h-9 rounded-xl border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-primary-600 hover:text-white hover:border-primary-500 hover:rotate-12 transition-all shadow-sm">
                                        <i class="fa-solid fa-sliders text-xs"></i>
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

<div id="modal-container"></div>
