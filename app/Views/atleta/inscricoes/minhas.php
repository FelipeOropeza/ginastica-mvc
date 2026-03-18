<?php $this->layout('layouts/athlete', ['title' => $title]) ?>

<div class="mb-8">
    <h2 class="text-2xl font-outfit font-bold text-slate-800">Minhas Inscrições</h2>
    <p class="text-slate-500 text-sm">Acompanhe seu histórico de participações e resultados.</p>
</div>

<?php if (empty($inscricoes)): ?>
    <div class="card p-12 text-center border-dashed border-2 bg-slate-50/30">
        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-300">
            <i class="fa-solid fa-list-check text-3xl"></i>
        </div>
        <h3 class="text-slate-600 font-bold mb-1 font-outfit">Você ainda não se inscreveu</h3>
        <p class="text-slate-400 text-sm mb-6 max-w-sm mx-auto">Explore as competições abertas e realize sua primeira inscrição hoje mesmo!</p>
        <a href="<?= route('atleta.competicoes.index') ?>" class="btn btn-primary px-8">Ver Competições</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 gap-4">
        <?php foreach ($inscricoes as $ins): ?>
            <div class="card p-4 hover:shadow-md transition-all duration-300 border-l-4 <?= $ins->status === 'confirmada' ? 'border-l-green-500' : 'border-l-amber-500' ?>">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-primary-600 text-xl font-bold">
                            <i class="fa-solid fa-person-gymnastics"></i>
                        </div>
                        <div>
                            <h3 class="font-outfit font-bold text-slate-800 uppercase text-sm tracking-tight"><?= e(str_replace('_', ' ', $ins->prova->aparelho)) ?></h3>
                            <p class="text-xs text-slate-500"><?= e($ins->competicao->nome ?? '...') ?></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-8">
                        <div class="text-center md:text-left">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Data</p>
                            <p class="text-xs font-medium text-slate-700"><?= date('d/m/y', strtotime($ins->inscrito_em)) ?></p>
                        </div>
                        
                        <div class="text-center md:text-left">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Status</p>
                            <?php 
                                $statusMap = [
                                    'pendente' => ['label' => 'Pendente', 'class' => 'bg-amber-100 text-amber-700'],
                                    'confirmada' => ['label' => 'Confirmada', 'class' => 'bg-green-100 text-green-700'],
                                    'desclassificada' => ['label' => 'Desclassificada', 'class' => 'bg-red-100 text-red-700'],
                                    'retirada' => ['label' => 'Retirada', 'class' => 'bg-slate-100 text-slate-700'],
                                ];
                                $s = $statusMap[$ins->status] ?? ['label' => $ins->status, 'class' => 'bg-slate-100 text-slate-700'];
                            ?>
                            <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest <?= $s['class'] ?>">
                                <?= $s['label'] ?>
                            </span>
                        </div>

                        <?php if ($ins->resultado): ?>
                            <div class="text-center md:text-right bg-primary-50 px-4 py-2 rounded-lg border border-primary-100">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-primary-600 mb-0.5">Nota Final</p>
                                <p class="text-lg font-outfit font-bold text-primary-900"><?= number_format((float)$ins->resultado->nota_final, 3) ?></p>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center gap-2">
                                <form action="<?= route('atleta.inscricoes.destroy', ['id' => $ins->id]) ?>" method="POST" 
                                      onsubmit="return confirm('Tem certeza que deseja cancelar esta inscrição?')" class="m-0">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn bg-red-50 text-red-500 hover:bg-red-500 hover:text-white px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest transition-all">
                                        <i class="fa-solid fa-xmark mr-1"></i> Desinscrever
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($ins->competicao->status === 'encerrada' && !empty($ins->notas)): ?>
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3"><i class="fa-solid fa-magnifying-glass-chart mr-1"></i> Detalhamento das Notas</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                            <?php foreach ($ins->notas as $nota): ?>
                                <div class="bg-slate-50 border border-slate-100 p-3 rounded-lg flex flex-col items-center text-center">
                                    <span class="text-[9px] font-bold uppercase text-slate-400 mb-1 tracking-wider"><?= e(str_replace('_', ' ', $nota->criterio)) ?></span>
                                    <span class="text-base font-outfit font-bold <?= $nota->criterio === 'penalidade' ? 'text-red-500' : 'text-slate-800' ?>"><?= number_format((float)$nota->valor, 3) ?></span>
                                    <span class="text-[9px] text-slate-500 mt-1 w-full truncate" title="<?= e($nota->jurado->nome ?? 'Jurado') ?>"><?= e($nota->jurado->nome ?? 'Jurado') ?></span>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if ($ins->resultado): ?>
                            <div class="bg-primary-50 border border-primary-100 p-3 rounded-lg flex flex-col items-center text-center ring-1 ring-primary-500/20">
                                <span class="text-[9px] font-bold uppercase text-primary-600 mb-1 tracking-wider">Média Final</span>
                                <span class="text-base font-outfit font-bold text-primary-900"><?= number_format((float)$ins->resultado->nota_final, 3) ?></span>
                                <span class="text-[9px] text-primary-600 mt-1 w-full truncate">Sistema</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
