<div class="h-full flex flex-col items-stretch animate-in fade-in duration-500">
    
    <div class="flex items-end justify-between mb-8 border-b border-white/10 pb-6">
        <div class="flex items-center gap-6">
            <h2 class="text-5xl font-outfit font-black text-white capitalize leading-none tracking-tighter italic">
                <?= str_replace('_', ' ', e($prova->aparelho)) ?>
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-primary-500/10 text-[10px] font-black text-primary-400 uppercase tracking-widest border border-primary-500/20">
                    <?= e($prova->categoria->nome ?? 'Todas') ?>
                </span>
                <span class="px-2 py-0.5 rounded bg-white/5 text-[10px] font-bold text-slate-500 uppercase tracking-widest border border-white/5">
                    <?= e($prova->subcategoria ?? 'Geral') ?>
                </span>
            </div>
        </div>

        <div class="text-right">
            <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest block mb-1">Total de Atletas</span>
            <p class="text-3xl font-outfit font-black text-white leading-none"><?= count($inscricoes) ?></p>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <?php 
        $isFig = ($prova->tipo_calculo === 'nota_d_mais_e');
        $isOlympic = ($prova->tipo_calculo === 'media_sem_extremos');
    ?>
    <div class="flex-1 min-h-0 overflow-y-auto scrollbar-hide">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[11px] font-black text-slate-600 uppercase tracking-[0.2em] border-b border-white/5">
                    <th class="px-4 py-4">Pos</th>
                    <th class="py-4">Atleta</th>
                    <th class="py-4">Equipe</th>
                    
                    <?php if ($isFig): ?>
                        <th class="py-4 text-center">D</th>
                        <th class="py-4 text-center">E</th>
                    <?php else: ?>
                        <th class="py-4 text-center">Média</th>
                    <?php endif; ?>

                    <th class="py-4 text-center">Pen</th>
                    <th class="px-6 py-4 text-right">Nota Final</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php foreach ($inscricoes as $i => $ins): ?>
                    <?php 
                        $res = $ins->resultado;
                        $pos = $res->classificacao ?? ($i + 1);
                        $isWinner = $pos === 1;
                    ?>
                    <tr class="group transition-colors <?= $isWinner ? 'bg-primary-500/5' : '' ?>">
                        <td class="px-4 py-5">
                            <span class="text-2xl font-outfit font-black <?= $isWinner ? 'text-primary-500' : 'text-slate-700' ?>">
                                <?= str_pad((string)$pos, 2, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td class="py-5">
                            <h4 class="text-xl font-bold text-white tracking-tight leading-none group-hover:text-primary-400 transition-colors">
                                <?= e($ins->atleta->nome_completo) ?>
                            </h4>
                        </td>
                        <td class="py-5">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <?= e($ins->atleta->equipe->nome ?? 'Avulso') ?>
                            </span>
                        </td>

                        <?php if ($isFig): ?>
                            <td class="py-5 text-center font-mono font-bold text-slate-400">
                                <?= $res ? number_format($res->nota_d, 3) : '-' ?>
                            </td>
                            <td class="py-5 text-center font-mono font-bold text-slate-400">
                                <?= $res ? number_format($res->nota_e, 3) : '-' ?>
                            </td>
                        <?php else: ?>
                            <td class="py-5 text-center font-mono font-bold text-slate-400">
                                <?php 
                                    $mediaDisplay = 0;
                                    if ($res) {
                                        $mediaDisplay = ($res->nota_final ?? 0) + ($res->penalidade ?? 0);
                                    }
                                ?>
                                <?= $res ? number_format($mediaDisplay, 3) : '-' ?>
                            </td>
                        <?php endif; ?>

                        <td class="py-5 text-center font-mono font-bold text-rose-500/50">
                            <?= $res ? number_format($res->penalidade, 3) : '-' ?>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <span class="text-3xl font-outfit font-black <?= $isWinner ? 'text-primary-500' : 'text-white' ?>">
                                <?= $res ? number_format($res->nota_final, 3) : '0.000' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

