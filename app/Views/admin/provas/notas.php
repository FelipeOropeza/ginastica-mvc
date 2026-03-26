<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex items-center gap-3 mb-6">
    <a href="/admin/competicoes/<?= $competicao->id ?>/provas" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-white hover:text-slate-600 transition-all">
        <i class="fa-solid fa-arrow-left text-xs"></i>
    </a>
    <div class="flex-1">
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Gerenciar Notas</h2>
        <p class="text-xs text-slate-500 font-medium tracking-tight uppercase">
            <?= str_replace('_', ' ', e($prova->aparelho)) ?> — <?= e($competicao->nome) ?>
        </p>
    </div>
    <?php if ($prova->encerrada): ?>
        <span class="px-3 py-1 rounded-lg bg-rose-100 text-rose-600 text-[10px] font-black uppercase tracking-widest border border-rose-200">
            <i class="fa-solid fa-lock mr-1"></i> Prova Encerrada
        </span>
    <?php else: ?>
        <span class="px-3 py-1 rounded-lg bg-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-200">
            <i class="fa-solid fa-lock-open mr-1"></i> Prova Aberta
        </span>
    <?php endif; ?>
</div>

<?php
    $tipoLabel = match($prova->tipo_calculo) {
        'nota_d_mais_e' => 'Sistema FIG (D+E)',
        'media_sem_extremos' => 'Média Olímpica',
        default => 'Média Aritmética'
    };
    $compEncerrada = $competicao->status === 'encerrada';
?>

<?php if ($compEncerrada): ?>
    <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-3">
        <i class="fa-solid fa-shield-halved text-rose-500"></i>
        <span class="text-[11px] font-bold text-rose-700">Competição encerrada — os resultados são definitivos e não podem ser alterados.</span>
    </div>
<?php endif; ?>

<div class="card p-4 mb-6 bg-slate-50 border-none shadow-sm flex flex-wrap items-center gap-6">
    <div>
        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-0.5">Sistema</span>
        <span class="px-2 py-0.5 rounded bg-slate-900 text-white text-[10px] font-bold uppercase"><?= $tipoLabel ?></span>
    </div>
    <div>
        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-0.5">Jurados</span>
        <span class="text-sm font-bold text-slate-800"><?= $prova->num_jurados ?></span>
    </div>
    <div>
        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-0.5">Inscritos</span>
        <span class="text-sm font-bold text-slate-800"><?= count($inscricoes) ?></span>
    </div>
</div>

<?php if (empty($inscricoes)): ?>
    <div class="card p-12 text-center border-dashed border-2">
        <i class="fa-solid fa-clipboard-list text-3xl text-slate-200 mb-3 block"></i>
        <p class="text-slate-400 text-sm font-medium">Nenhuma inscrição nesta prova.</p>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($inscricoes as $ins): ?>
            <?php
                $notasAtleta = array_filter($notas, fn($n) => $n->inscricao_id == $ins->id);
                $resultado = $ins->resultado;
                $isPendente = $resultado && !$resultado->calculado;
                $isSemNotas = !$resultado && empty($notasAtleta);
            ?>
            <div class="card p-5 <?= ($isPendente || $isSemNotas) ? 'border-amber-200 bg-amber-50/20' : '' ?>"><?php if ($isPendente || ($isSemNotas && $prova->encerrada)): ?>
                    <div class="mb-3 px-3 py-1.5 rounded-lg bg-amber-100 border border-amber-200 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-amber-600 text-xs animate-pulse"></i>
                        <span class="text-[10px] font-black text-amber-700 uppercase tracking-wider">Aguardando juiz re-enviar nota</span>
                    </div>
                <?php endif; ?>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-sm font-black border border-slate-200">
                            <?= $ins->ordem_apresentacao ?: '#' ?>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm"><?= e($ins->atleta->nome_completo) ?></h4>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-tight"><?= e($ins->atleta->equipe->nome ?? 'Avulso') ?></p>
                        </div>
                    </div>
                    <?php if ($resultado): ?>
                        <div class="text-right">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">Nota Final</span>
                            <span class="text-lg font-outfit font-black <?= $resultado->calculado ? 'text-emerald-600' : 'text-amber-600' ?>">
                                <?= number_format($resultado->nota_final, 3) ?>
                            </span>
                            <?php if (!$resultado->calculado): ?>
                                <span class="block text-[8px] font-bold text-amber-500 uppercase">Parcial</span>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col items-end gap-2 text-right">
                            <span class="text-[10px] font-bold text-slate-300 uppercase">Sem notas</span>
                            <?php if (!$compEncerrada): ?>
                                <form action="/admin/inscricoes/<?= $ins->id ?>/reabrir" method="POST"
                                      onsubmit="return confirm('Autorizar juízes a lançar nota para este atleta? Ele aparecerá novamente na fila de avaliação.')">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-primary-50 text-primary-600 hover:bg-primary-100 border border-primary-100 text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                                        <i class="fa-solid fa-cloud-arrow-up mr-1 text-xs"></i> Autorizar Lançamento
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($notasAtleta)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-slate-100">
                                    <th class="pb-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jurado</th>
                                    <th class="pb-2 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Critério</th>
                                    <th class="pb-2 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Valor</th>
                                    <th class="pb-2 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Registrado</th>
                                    <th class="pb-2 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php foreach ($notasAtleta as $nota): ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-2.5">
                                            <span class="font-bold text-slate-700 text-xs"><?= e($nota->jurado->nome ?? 'Jurado #' . $nota->jurado_id) ?></span>
                                        </td>
                                        <td class="py-2.5 text-center">
                                            <?php
                                                $criterioLabel = str_replace(
                                                    ['nota_d', 'nota_e', 'geral', 'arbitro_superior'],
                                                    ['Nota D', 'Nota E', 'Geral', 'Árbitro Sup.'],
                                                    $nota->criterio
                                                );
                                                $criterioColor = match($nota->criterio) {
                                                    'nota_d' => 'bg-blue-100 text-blue-700',
                                                    'nota_e' => 'bg-emerald-100 text-emerald-700',
                                                    default => 'bg-primary-100 text-primary-700'
                                                };
                                            ?>
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase <?= $criterioColor ?>"><?= $criterioLabel ?></span>
                                        </td>
                                        <td class="py-2.5 text-center">
                                            <span class="font-outfit font-black text-slate-800"><?= number_format($nota->valor, 3) ?></span>
                                        </td>
                                        <td class="py-2.5 text-center text-[10px] text-slate-400">
                                            <?= $nota->registrado_em ? date('d/m H:i', strtotime($nota->registrado_em)) : '-' ?>
                                        </td>
                                        <td class="py-2.5 text-right">
                                            <?php if ($compEncerrada): ?>
                                                <span class="text-[9px] text-slate-300"><i class="fa-solid fa-lock"></i></span>
                                            <?php else: ?>
                                                <form action="/admin/notas/<?= $nota->id ?>/reabrir" method="POST" class="inline"
                                                      onsubmit="return confirm('Reabrir esta nota? O jurado precisará reavaliar.')">
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200 text-[9px] font-black uppercase tracking-wider transition-all">
                                                        <i class="fa-solid fa-rotate-left mr-1"></i> Reabrir
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-[10px] text-slate-300 font-bold uppercase text-center py-3">Nenhuma nota registrada para este atleta.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
