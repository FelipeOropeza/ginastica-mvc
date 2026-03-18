<?php $this->layout('layouts/athlete', ['title' => $title]) ?>

<?php if ($perfilIncompleto): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 mb-8 flex flex-col md:flex-row items-center gap-6 animate-pulse">
        <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
            <i class="fa-solid fa-address-card text-2xl"></i>
        </div>
        <div class="flex-1 text-center md:text-left">
            <h3 class="text-amber-900 font-bold text-lg mb-1">Perfil em Análise!</h3>
            <p class="text-amber-700 text-sm leading-relaxed">
                Para se inscrever em competições, precisamos que você complete seu cadastro com sua data de nascimento, equipe e categoria.
            </p>
        </div>
        <a href="<?= route('atleta.profile') ?>" class="btn bg-amber-600 text-white hover:bg-amber-700 shadow-lg shadow-amber-600/20 px-6 py-3 shrink-0">
            Completar Agora
        </a>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Card Perfil -->
    <div class="card p-6 bg-gradient-to-br from-primary-600 to-primary-800 text-white border-none relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-xs font-bold uppercase tracking-widest opacity-80 mb-1">Atleta,</h3>
            <p class="text-2xl font-outfit font-bold mb-4"><?= e(session('user')['nome']) ?></p>
            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3 inline-block">
                <p class="text-[10px] font-bold uppercase tracking-tighter text-white">Status: 
                    <span class="<?= ($atleta->ativo ?? false) ? 'text-green-300' : 'text-amber-300' ?>">
                        <?= ($atleta->ativo ?? false) ? 'Regularizado' : 'Aguardando Perfil' ?>
                    </span>
                </p>
            </div>
        </div>
        <i class="fa-solid fa-medal absolute -right-4 -bottom-4 text-7xl text-white/10 -rotate-12"></i>
    </div>

    <!-- Inscrições -->
    <div class="card p-6 flex flex-col justify-center text-center">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Minhas Provas</p>
        <p class="text-4xl font-outfit font-black text-slate-800"><?= $totalInscricoes ?></p>
        <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-tighter">Inscrições Confirmadas</p>
    </div>

    <!-- Melhor Nota -->
    <div class="card p-6 flex flex-col justify-center text-center">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Minha Melhor Nota</p>
        <p class="text-4xl font-outfit font-black text-slate-800"><?= $melhorNota !== '--' ? number_format($melhorNota, 3) : '--' ?></p>
        <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-tighter">Histórico Geral</p>
    </div>
</div>

<div class="mt-8">
    <div class="flex items-center justify-between mb-4 px-2">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Minhas Atividades Recentes</h3>
        <a href="<?= route('atleta.dashboard') ?>" class="text-[10px] font-bold text-primary-600 hover:text-primary-700 uppercase tracking-widest">Ver Tudo</a>
    </div>

    <?php if (empty($atividades)): ?>
        <div class="card p-12 text-center border-dashed border-2 bg-slate-50/30">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="fa-solid fa-ghost text-2xl"></i>
            </div>
            <h4 class="text-slate-600 font-bold mb-1">Nada por aqui ainda...</h4>
            <p class="text-slate-400 text-xs italic mx-auto max-w-xs">Participe de uma competição para ver seu histórico de apresentações e notas aqui.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($atividades as $ativ): ?>
                <div class="card p-4 hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 
                        <?= $ativ->resultado ? 'bg-green-100 text-green-600' : 'bg-primary-100 text-primary-600' ?>">
                        <i class="fa-solid <?= $ativ->resultado ? 'fa-check-double' : 'fa-clipboard-check' ?> text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-0.5">
                            <h4 class="font-bold text-slate-800 text-sm truncate"><?= e($ativ->competicao->nome) ?></h4>
                            <p class="text-[10px] font-bold text-slate-400 tracking-tighter shrink-0"><?= date('d/m/Y', strtotime($ativ->inscrito_em ?? $ativ->created_at)) ?></p>
                        </div>
                        <p class="text-xs text-slate-500 lowercase leading-none">
                            <span class="font-bold uppercase text-[10px]">Aparelho:</span> <?= e(str_replace('_', ' ', $ativ->prova->aparelho)) ?>
                        </p>
                    </div>
                    <?php if ($ativ->resultado): ?>
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter leading-none mb-1">Nota Final</p>
                            <p class="text-lg font-outfit font-black text-green-600 leading-none"><?= number_format($ativ->resultado->nota_final, 3) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-2">
                            <div class="bg-slate-100 px-3 py-1.5 rounded-lg">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">Inscrito</p>
                            </div>
                            <form action="<?= route('atleta.inscricoes.destroy', ['id' => $ativ->id]) ?>" method="POST" 
                                  onsubmit="return confirm('Tem certeza que deseja cancelar esta inscrição?')" class="m-0">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center" 
                                        title="Cancelar Inscrição">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
