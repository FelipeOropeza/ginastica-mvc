<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <a href="<?= route('admin.relatorios.index') ?>" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-slate-900"><?= e($competition->nome) ?></h1>
        </div>
        <p class="text-sm text-slate-500 ml-10">
            <?= date('d/m/Y', strtotime($competition->data_inicio)) ?> - <?= e($competition->local) ?>
        </p>
    </div>
    
    <?php if (!empty($competition->provas)): ?>
        <a href="<?= route('admin.relatorios.competicao.csv', ['id' => $competition->id]) ?>" 
           class="btn btn-primary flex items-center gap-2">
            <i class="fa-solid fa-file-csv"></i>
            Exportar CSV
        </a>
    <?php endif; ?>
</div>

<?php if (empty($competition->provas)): ?>
    <div class="card">
        <div class="p-12 text-center text-slate-400">
            <i class="fa-solid fa-clipboard-list text-4xl mb-3"></i>
            <p class="font-medium">Nenhuma prova cadastrada nesta competição.</p>
            <a href="<?= route('admin.competicoes.edit', ['id' => $competition->id]) ?>" class="text-primary-600 hover:underline mt-2 inline-block">
                Cadastrar provas
            </a>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($competition->provas as $prova): ?>
        <div class="card mb-4">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800 capitalize">
                        <?= str_replace('_', ' ', e($prova->aparelho)) ?>
                    </h2>
                    <?php if ($prova->categoria): ?>
                        <p class="text-sm text-slate-500 font-medium"><?= e($prova->categoria->nome) ?></p>
                    <?php endif; ?>
                    <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest mt-1">
                        <?= $prova->tipo_calculo === 'nota_d_mais_e' ? 'Sistema FIG (D+E)' : ($prova->tipo_calculo === 'media_sem_extremos' ? 'Média Olímpica' : 'Média Aritmética') ?>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-slate-500">
                        <?= count($prova->inscricoes) ?> inscritos
                    </p>
                </div>
            </div>
            
            <div class="p-5">
                <?php
                $results = [];
                foreach ($prova->inscricoes as $inscricao) {
                    if ($inscricao->resultado) {
                        $results[] = [
                            'inscricao' => $inscricao,
                            'atleta' => $inscricao->atleta,
                            'equipe' => $inscricao->atleta?->equipe,
                            'resultado' => $inscricao->resultado,
                        ];
                    }
                }
                usort($results, fn($a, $b) => ($a['resultado']->classificacao ?? 999) <=> ($b['resultado']->classificacao ?? 999));
                ?>
                
                <?php if (empty($results)): ?>
                    <p class="text-slate-400 text-center py-8">Nenhum resultado registrado.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-500 border-b border-slate-100">
                                    <th class="pb-3 font-medium w-16">Class.</th>
                                    <th class="pb-3 font-medium">Atleta</th>
                                    <th class="pb-3 font-medium">Equipe</th>
                                    
                                    <?php if ($prova->tipo_calculo === 'nota_d_mais_e'): ?>
                                        <th class="pb-3 font-medium text-center">Nota D</th>
                                        <th class="pb-3 font-medium text-center">Nota E</th>
                                    <?php else: ?>
                                        <th class="pb-3 font-medium text-center">Média</th>
                                    <?php endif; ?>

                                    <th class="pb-3 font-medium text-center">Pen.</th>
                                    <th class="pb-3 font-medium text-center">Final</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php foreach ($results as $row): ?>
                                    <?php
                                    $podioClass = match($row['resultado']->podio) {
                                        'ouro' => 'bg-yellow-100 text-yellow-700',
                                        'prata' => 'bg-slate-100 text-slate-600',
                                        'bronze' => 'bg-orange-100 text-orange-700',
                                        default => ''
                                    };
                                    ?>
                                    <tr class="hover:bg-slate-50 <?= $podioClass ?>">
                                        <td class="py-3">
                                            <?php if ($row['resultado']->classificacao): ?>
                                                <span class="font-bold <?= $row['resultado']->classificacao === 1 ? 'text-yellow-600' : '' ?>">
                                                    <?= $row['resultado']->classificacao ?>º
                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 font-medium text-slate-800">
                                            <?php if ($row['atleta']): ?>
                                                <a href="<?= route('admin.relatorios.atleta', ['id' => $row['atleta']->id]) ?>" 
                                                   class="hover:text-primary-600">
                                                    <?= e($row['atleta']->nome_completo) ?>
                                                </a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 text-slate-600">
                                            <?= e($row['equipe']?->nome ?? '-') ?>
                                        </td>

                                        <?php if ($prova->tipo_calculo === 'nota_d_mais_e'): ?>
                                            <td class="py-3 text-center font-mono">
                                                <?= number_format($row['resultado']->nota_d ?? 0, 3) ?>
                                            </td>
                                            <td class="py-3 text-center font-mono">
                                                <?= number_format($row['resultado']->nota_e ?? 0, 3) ?>
                                            </td>
                                        <?php else: ?>
                                            <td class="py-3 text-center font-mono">
                                                <?= number_format($row['resultado']->nota_d ?? 0, 3) ?>
                                            </td>
                                        <?php endif; ?>

                                        <td class="py-3 text-center font-mono text-red-500">
                                            <?= number_format($row['resultado']->penalidade ?? 0, 3) ?>
                                        </td>
                                        <td class="py-3 text-center font-bold font-mono">
                                            <?= number_format($row['resultado']->nota_final ?? 0, 3) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
