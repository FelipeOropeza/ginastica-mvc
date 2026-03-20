<?php $this->layout('layouts/athlete', ['title' => $title]) ?>

<!-- Alerta de Perfil Incompleto -->
<?php if ($perfilIncompleto): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 mb-8 flex flex-col md:flex-row items-center gap-6 animate-pulse">
        <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0 shadow-sm">
            <i class="fa-solid fa-address-card text-2xl"></i>
        </div>
        <div class="flex-1 text-center md:text-left">
            <h3 class="text-amber-900 font-bold text-lg mb-1 font-outfit">Perfil Pendente!</h3>
            <p class="text-amber-700 text-sm leading-relaxed">
                Para participar das competições, seu perfil precisa estar completo (Equipe, Categoria e Data de Nasc.).
            </p>
        </div>
        <a href="<?= route('atleta.profile') ?>" class="btn bg-amber-600 text-white hover:bg-amber-700 shadow-lg shadow-amber-600/20 px-6 py-3 shrink-0 rounded-xl font-bold uppercase text-xs tracking-widest">
            Completar Meu Perfil
        </a>
    </div>
<?php endif; ?>

<!-- Resumo em Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="card p-6 bg-slate-900 text-white border-none relative overflow-hidden shadow-xl">
        <div class="relative z-10">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-primary-400 mb-4">Membro Oficial</h3>
            <p class="text-3xl font-outfit font-black mb-1 truncate leading-none"><?= e(explode(' ', session('user')['nome'])[0]) ?></p>
            <p class="text-xs text-slate-400 font-medium mb-1"><?= e($atleta->equipe->nome ?? 'Atleta Avulso') ?></p>
            
            <?php if (!empty($atleta->equipe->treinadores)): ?>
                <div class="flex items-center gap-1.5 mb-4 text-emerald-400">
                    <i class="fa-solid fa-user-tie text-[9px]"></i>
                    <span class="text-[9px] font-black uppercase tracking-widest">Técnico: <?= e($atleta->equipe->treinadores[0]->nome_completo ?? 'Responsável') ?></span>
                </div>
            <?php else: ?>
                <div class="mb-4"></div>
            <?php endif; ?>

            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800 border border-white/5 text-[9px] font-black uppercase tracking-widest">
                <span class="w-1.5 h-1.5 rounded-full <?= ($atleta->ativo ?? false) ? 'bg-green-500' : 'bg-amber-500' ?>"></span>
                <?= ($atleta->ativo ?? false) ? 'Ativo' : 'Pendente' ?>
            </span>
        </div>
        <i class="fa-solid fa-medal absolute -right-4 -bottom-4 text-6xl text-white/5 -rotate-12 translate-y-4"></i>
    </div>

    <div class="card p-6 flex flex-col justify-center text-center shadow-sm bg-white border-slate-100">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 leading-none">Inscrições</p>
        <p class="text-5xl font-outfit font-black text-slate-800 leading-none"><?= $totalInscricoes ?></p>
        <div class="mt-4">
            <a href="<?= route('atleta.inscricoes.minhas') ?>" class="text-[9px] font-black text-slate-400 uppercase tracking-widest hover:text-primary-600 transition-colors">Ver Histórico</a>
        </div>
    </div>

    <div class="card p-6 flex flex-col justify-center text-center shadow-sm bg-white border-slate-100">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 leading-none">Melhor Nota</p>
        <p class="text-5xl font-outfit font-black text-primary-600 leading-none"><?= $melhorNota !== '--' ? number_format($melhorNota, 3) : '--' ?></p>
        <div class="mt-4">
            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest leading-none">Melhor nota enviada</p>
        </div>
    </div>
</div>

<div class="mt-12">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-outfit font-black text-slate-800 tracking-tight leading-none mb-1">Últimas Participações</h3>
            <p class="text-xs text-slate-400 font-medium">Suas atividades mais recentes no sistema.</p>
        </div>
        <a href="<?= route('atleta.inscricoes.minhas') ?>" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-primary-600 transition-colors">Ver Tudo <i class="fa-solid fa-arrow-right-long ml-1"></i></a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if (empty($atividades)): ?>
            <div class="md:col-span-2 card p-12 text-center border-dashed border-2 bg-slate-50/50">
                <i class="fa-solid fa-clipboard-list text-slate-200 text-3xl mb-3"></i>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Sem atividades recentes</p>
            </div>
        <?php else: ?>
            <?php foreach ($atividades as $ativ): ?>
                <div class="card p-4 hover:shadow-lg transition-all flex items-center gap-5 bg-white border-slate-100 group">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 text-slate-400 flex items-center justify-center shrink-0 group-hover:bg-primary-50 group-hover:text-primary-600 group-hover:border-primary-100 transition-all duration-300">
                        <i class="fa-solid <?= $ativ->resultado ? 'fa-award' : 'fa-clipboard-check' ?> text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-xs truncate mb-0.5 group-hover:text-primary-700 transition-colors uppercase leading-none"><?= e($ativ->competicao->nome) ?></h4>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">
                            <?= e(str_replace('_', ' ', $ativ->prova->aparelho)) ?>
                        </p>
                    </div>
                    <?php if ($ativ->resultado): ?>
                        <div class="text-right">
                            <p class="text-lg font-outfit font-black text-primary-600 leading-none"><?= number_format($ativ->resultado->nota_final, 3) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="px-3 py-1 rounded-lg bg-slate-100 text-[8px] font-black text-slate-500 uppercase tracking-tighter">
                            Inscrito
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-8 overflow-hidden border-none shadow-xl bg-slate-900 group relative">
    <div class="px-8 py-10 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div class="max-w-md">
            <h3 class="text-2xl font-outfit font-bold text-white mb-2 tracking-tight">Pronto para o próximo desafio?</h3>
            <p class="text-slate-400 text-sm font-medium">Acesse o calendário completo para ver todas as competições e garantir sua vaga.</p>
        </div>
        <a href="<?= route('atleta.competicoes.index') ?>" class="btn bg-primary-600 text-white hover:bg-primary-700 shadow-xl shadow-primary-600/30 px-10 py-4 rounded-2xl font-black uppercase text-xs tracking-[0.2em] transform transition-all group-hover:scale-105 active:scale-95">
            Abrir Calendário <i class="fa-solid fa-arrow-right ml-2 opacity-50"></i>
        </a>
    </div>
    <i class="fa-solid fa-trophy absolute -right-6 -bottom-6 text-9xl text-white/5 -rotate-12 transition-transform duration-700 group-hover:rotate-0 pointer-events-none"></i>
</div>

<div id="modal-container"></div>
