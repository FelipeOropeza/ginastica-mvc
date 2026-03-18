<?php $this->layout('layouts/athlete', ['title' => $title]) ?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-outfit font-bold text-slate-800 tracking-tight"><?= e($competicao->nome) ?></h2>
        <div class="flex items-center gap-4 mt-2">
            <div class="flex items-center gap-2 text-slate-500 text-sm">
                <i class="fa-solid fa-calendar-day text-primary-500"></i>
                <?= date('d/m/Y', strtotime($competicao->data_inicio)) ?>
                <?php if ($competicao->data_fim): ?>
                    - <?= date('d/m/Y', strtotime($competicao->data_fim)) ?>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-2 text-slate-500 text-sm border-l border-slate-200 pl-4">
                <i class="fa-solid fa-location-dot text-primary-500"></i>
                <?= e($competicao->local ?? 'Local a definir') ?>
            </div>
        </div>
    </div>
    <a href="<?= route('atleta.competicoes.index') ?>" class="btn bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">
        <i class="fa-solid fa-arrow-left mr-2"></i> Voltar
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Descrição e Detalhes -->
    <div class="lg:col-span-2 space-y-8">
        <div class="card p-8">
            <h3 class="text-xl font-outfit font-bold text-slate-800 mb-4 pb-4 border-b border-slate-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-info text-primary-600"></i> Sobre a Competição
            </h3>
            <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">
                <?= nl2br(e($competicao->descricao ?? 'Nenhuma descrição detalhada disponível.')) ?>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xl font-outfit font-bold text-slate-800 flex items-center gap-3">
                    <i class="fa-solid fa-trophy text-amber-500"></i> Provas Disponíveis
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-primary-100 text-primary-700 font-bold uppercase tracking-widest ml-2">
                        Minha Categoria: <?= e($atleta->categoria->nome ?? 'Sua Categoria') ?>
                    </span>
                </h3>
            </div>
            
            <div class="divide-y divide-slate-100">
                <?php if (empty($provas)): ?>
                    <div class="p-12 text-center text-slate-400">
                        <i class="fa-solid fa-ban text-4xl mb-4 block opacity-20"></i>
                        Não existem provas abertas para sua categoria nesta competição.
                    </div>
                <?php else: ?>
                    <?php foreach ($provas as $prova): ?>
                        <div class="p-6 flex items-center justify-between hover:bg-slate-50/80 transition-colors group">
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 rounded-2xl bg-white border-2 border-slate-100 text-primary-600 flex items-center justify-center text-2xl group-hover:border-primary-500 group-hover:scale-105 transition-all duration-300">
                                    <i class="fa-solid fa-person-gymnastics"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-outfit font-bold text-slate-800 uppercase tracking-tight">
                                        <?= e(str_replace('_', ' ', $prova->aparelho)) ?>
                                    </h4>
                                    <?php if ($prova->descricao): ?>
                                        <p class="text-[10px] text-slate-400 font-medium uppercase mb-1"><?= e($prova->descricao) ?></p>
                                    <?php endif; ?>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        <?= e($prova->num_jurados ?? '...') ?> jurados | 
                                        <?= $prova->tipo_calculo === 'nota_d_mais_e' ? 'Regra FIG (D+E)' : 'Média Simples' ?>
                                        <?php if ($prova->max_participantes): ?>
                                            | <span class="<?= $prova->vagas_ocupadas >= $prova->max_participantes ? 'text-red-500 font-bold' : '' ?>">
                                                <?= $prova->vagas_ocupadas ?>/<?= $prova->max_participantes ?> vagas
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <?php if (in_array($prova->id, $idsProvasInscritas)): ?>
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-green-100 text-green-700 font-bold text-xs uppercase cursor-default">
                                        <i class="fa-solid fa-check-circle"></i> Inscrito
                                    </span>
                                <?php elseif ($prova->max_participantes && $prova->vagas_ocupadas >= $prova->max_participantes): ?>
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-slate-100 text-slate-400 font-bold text-xs uppercase cursor-not-allowed border border-slate-200">
                                        <i class="fa-solid fa-users-slash"></i> Vagas Esgotadas
                                    </span>
                                <?php else: ?>
                                    <form action="<?= route('atleta.inscricoes.store') ?>" method="POST" class="m-0">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="prova_id" value="<?= $prova->id ?>">
                                        <input type="hidden" name="competicao_id" value="<?= $competicao->id ?>">
                                        <button type="submit" class="btn btn-primary px-6 border-none shadow-md shadow-primary-600/20 active:translate-y-0.5 transition-all">
                                            Inscrever-se
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar de Informações -->
    <div class="space-y-6">
        <div class="card p-6 bg-slate-900 border-none text-white shadow-xl shadow-slate-200">
            <h4 class="text-xs font-bold uppercase tracking-widest text-primary-400 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i> Informações Importantes
            </h4>
            <ul class="space-y-4">
                <li class="flex gap-3 text-sm">
                    <i class="fa-solid fa-check text-green-400 mt-1 shrink-0"></i>
                    <span class="opacity-80">Você só pode se inscrever em provas da sua categoria atual.</span>
                </li>
                <li class="flex gap-3 text-sm">
                    <i class="fa-solid fa-check text-green-400 mt-1 shrink-0"></i>
                    <span class="opacity-80">Cada inscrição será confirmada automaticamente se o perfil estiver ativo.</span>
                </li>
                <li class="flex gap-3 text-sm">
                    <i class="fa-solid fa-check text-green-400 mt-1 shrink-0"></i>
                    <span class="opacity-80">Fique atento ao horário das provas que será publicado em breve.</span>
                </li>
            </ul>
        </div>

        <div class="card p-6 bg-gradient-to-br from-primary-500/5 to-white">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Seu Perfil</p>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm">
                    <?= strtoupper(substr(session('user')['nome'], 0, 1)) ?>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-slate-800"><?= e(session('user')['nome']) ?></h5>
                    <p class="text-[10px] text-slate-500 uppercase tracking-tighter">
                        <?= e($atleta->equipe->nome ?? 'Sua Equipe') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
