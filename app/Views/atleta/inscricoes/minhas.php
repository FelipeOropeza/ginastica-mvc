<?php $this->layout('layouts/athlete', ['title' => $title]) ?>

<div class="mb-8">
    <h2 class="text-2xl font-outfit font-black text-slate-800 tracking-tight">Minhas Inscrições</h2>
    <p class="text-sm text-slate-500 font-medium">Histórico de provas solicitadas e resultados obtidos.</p>
</div>

<?php if (empty($inscricoes)): ?>
    <div class="card p-12 text-center border-dashed border-2 bg-slate-50/50">
        <div class="w-20 h-20 rounded-full bg-white shadow-sm flex items-center justify-center mx-auto mb-6 text-slate-300">
            <i class="fa-solid fa-clipboard-question text-3xl"></i>
        </div>
        <h4 class="text-slate-700 font-bold text-lg mb-2">Nenhuma inscrição encontrada</h4>
        <p class="text-slate-500 text-sm italic mx-auto max-w-sm">Você ainda não se inscreveu em nenhuma prova ou aparelho. Vá para a seção de Competições para começar!</p>
        <a href="<?= route('atleta.competicoes.index') ?>" class="mt-6 inline-flex btn btn-primary px-8 py-3 text-sm font-bold uppercase tracking-widest rounded-xl">
            Ver Calendário
        </a>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($inscricoes as $ins): ?>
            <div x-data="{ open: false }" class="space-y-2">
                <div @click="open = !open" 
                     class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center gap-6 group hover:border-primary-300 transition-all cursor-pointer select-none">
                    
                    <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center shrink-0 border border-slate-100 group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                        <i class="fa-solid <?= $ins->resultado ? 'fa-award' : 'fa-list-check' ?> text-xl"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1">
                            <h4 class="font-outfit font-black text-slate-800 text-lg leading-none truncate">
                                <?= e($ins->competicao->nome) ?>
                            </h4>
                            <?php if ($ins->competicao->status === 'encerrada'): ?>
                                <i class="fa-solid fa-magnifying-glass-chart text-[10px] text-primary-500 animate-pulse"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
                            <p class="text-slate-500 font-medium flex items-center gap-1.5 lowercase">
                                <span class="font-black uppercase text-[10px] text-slate-400 tracking-tighter mr-1 ml-0.5">Aparelho:</span> <?= e(str_replace('_', ' ', $ins->prova->aparelho)) ?>
                            </p>
                            <p class="text-slate-400 flex items-center gap-1.5 font-medium">
                               <span class="text-[10px] uppercase font-black tracking-tighter mr-1">Solicitado:</span> <?= date('d/m/Y', strtotime($ins->inscrito_em)) ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 shrink-0 w-full md:w-auto border-t md:border-t-0 pt-4 md:pt-0">
                        <?php if ($ins->resultado): ?>
                            <div class="flex items-center gap-4 bg-slate-50 px-5 py-3 rounded-xl border border-slate-100">
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Nota Final</p>
                                    <p class="text-2xl font-outfit font-black text-primary-600 leading-none"><?= number_format($ins->resultado->total ?? $ins->resultado->nota_final, 3) ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center gap-3">
                                <div class="bg-amber-50 text-amber-700 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest border border-amber-100 shadow-inner">
                                    Inscrição Efetuada
                                </div>
                                
                                <?php if ($ins->competicao->status === 'aberta'): ?>
                                    <form action="<?= route('atleta.inscricoes.destroy', ['id' => $ins->id]) ?>" method="POST" 
                                          onsubmit="event.stopPropagation(); return confirm('Tem certeza que deseja cancelar essa participação?')" class="m-0">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm flex items-center justify-center">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($ins->competicao->status === 'encerrada' && !empty($ins->notas)): ?>
                    <div x-show="open" x-collapse x-transition 
                         class="ml-6 md:ml-14 card p-5 bg-slate-50 border-none shadow-inner border-l-4 border-primary-500 animate-in slide-in-from-top-2 duration-300">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-chart-simple text-primary-500"></i> Detalhamento da Puntuação (Oficial)
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <?php foreach($ins->notas as $nota): ?>
                                <div class="bg-white p-3 rounded-xl border border-slate-100 flex items-center justify-between shadow-sm">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter leading-none mb-1"><?= str_replace('nota_', '', $nota->criterio) ?></p>
                                        <p class="text-[10px] text-slate-500 font-bold truncate max-w-[120px]"><?= e($nota->jurado->nome ?? 'Árbitro') ?></p>
                                    </div>
                                    <span class="text-sm font-outfit font-black text-slate-800"><?= number_format($nota->valor, 3) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div id="modal-container"></div>
