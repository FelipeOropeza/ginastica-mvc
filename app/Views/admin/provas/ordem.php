<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div id="ordem-full-container">
    <div class="flex items-center gap-3 mb-6" id="ordem-header">
        <a href="/admin/competicoes/<?= $competicao->id ?>/provas" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-white hover:text-slate-600 transition-all">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Ordem de Apresentação</h2>
            <p class="text-xs text-slate-500 font-medium tracking-tight uppercase"><?= $competicao->nome ?> &bull; <?= str_replace('_', ' ', $prova->aparelho) ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" id="management-container">
    <!-- Painel de Controle -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-6 border-none bg-slate-900 text-white shadow-xl shadow-slate-900/10">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-6 flex items-center gap-2 text-primary-400">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Ações Rápidas
            </h3>
            
            <p class="text-[11px] text-slate-400 mb-6 font-medium leading-relaxed">
                A ordem de apresentação define a sequência que os juízes verão na tela de avaliação. 
                Recomenda-se realizar o sorteio antes de iniciar a competição.
            </p>

            <form hx-post="<?= route('admin.provas.shuffle', ['id' => $prova->id]) ?>" 
                  hx-target="#ordem-full-container" 
                  hx-select="#ordem-full-container"
                  hx-confirm="Isso irá embaralhar todos os atletas inscritos nesta prova. Deseja continuar?">
                <button type="submit" class="w-full py-4 bg-primary-600 hover:bg-primary-500 text-white rounded-2xl font-bold text-xs uppercase tracking-widest transition-all shadow-lg shadow-primary-600/20 active:scale-95 flex items-center justify-center gap-3 group">
                    <i class="fa-solid fa-shuffle group-hover:rotate-180 transition-transform duration-500"></i>
                    Sortear Ordem Agora
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Resumo da Prova</span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-slate-400 font-bold uppercase">Confirmados</span>
                        <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] font-black text-white"><?= count($inscricoes) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-slate-400 font-bold uppercase">Juízes Designados</span>
                        <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] font-black text-white"><?= $prova->num_jurados ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-6 border-dashed border-2 bg-slate-50/50">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 leading-none">Status da Fila</h3>
            <?php 
                $semOrdem = array_filter($inscricoes, fn($i) => is_null($i->ordem_apresentacao));
                if (!empty($semOrdem)):
            ?>
                <div class="flex items-center gap-2 text-amber-600 mb-2">
                    <i class="fa-solid fa-circle-exclamation text-xs"></i>
                    <span class="text-[10px] font-black uppercase tracking-tight"><?= count($semOrdem) ?> Atletas sem ordem!</span>
                </div>
                <p class="text-[9px] text-slate-400 font-medium italic">Atletas sem ordem aparecerão no final da fila dos juízes.</p>
            <?php else: ?>
                <div class="flex items-center gap-2 text-emerald-600">
                    <i class="fa-solid fa-circle-check text-xs"></i>
                    <span class="text-[10px] font-black uppercase tracking-tight">Fila organizada com sucesso</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lista da Fila -->
    <div class="lg:col-span-2">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-grip-vertical text-slate-300"></i> Fila de Apresentação
            </h3>
        </div>

        <div class="space-y-3">
            <?php if (empty($inscricoes)): ?>
                <div class="card p-16 text-center border-dashed border-2">
                    <i class="fa-solid fa-user-slash text-slate-200 text-3xl mb-4"></i>
                    <p class="text-slate-400 text-sm font-medium">Nenhum atleta inscrito nesta prova ainda.</p>
                </div>
            <?php else: ?>
                <?php foreach ($inscricoes as $ins): ?>
                    <div class="card p-4 hover:shadow-lg transition-all border-none bg-white flex items-center gap-6 group">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 border border-slate-100 flex items-center justify-center text-sm font-black shadow-inner group-hover:bg-primary-600 group-hover:text-white group-hover:border-primary-600 transition-all">
                            <?= $ins->ordem_apresentacao ?: '-' ?>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h4 class="font-outfit font-black text-slate-800 text-base leading-tight mb-1"><?= e($ins->atleta->nome_completo) ?></h4>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-shield-halved text-slate-300"></i> <?= e($ins->atleta->equipe->nome ?? 'Avulso') ?>
                                </span>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full bg-slate-100 text-[9px] font-black text-slate-500 uppercase tracking-widest border border-slate-200/50">
                                <?= e($ins->status) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
