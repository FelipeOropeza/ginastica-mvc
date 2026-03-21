<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Relatórios</h1>
    <p class="text-sm text-slate-500 mt-1">Visualize e exporte resultados das competições</p>
</div>

<div class="card">
    <div class="p-5 border-b border-slate-100">
        <h2 class="text-lg font-semibold text-slate-800">Competições</h2>
    </div>
    
    <div class="p-5">
        <?php if (empty($competitions)): ?>
            <div class="text-center py-12 text-slate-400">
                <i class="fa-solid fa-chart-bar text-4xl mb-3"></i>
                <p>Nenhuma competição encontrada.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b border-slate-100">
                            <th class="pb-3 font-medium">Competição</th>
                            <th class="pb-3 font-medium">Período</th>
                            <th class="pb-3 font-medium text-center">Status</th>
                            <th class="pb-3 font-medium text-center">Provas</th>
                            <th class="pb-3 font-medium text-center">Inscritos</th>
                            <th class="pb-3 font-medium text-center">Resultados</th>
                            <th class="pb-3 font-medium text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($competitions as $comp): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4">
                                    <p class="font-semibold text-slate-800"><?= e($comp->nome) ?></p>
                                    <p class="text-xs text-slate-400 mt-0.5"><?= e($comp->local) ?></p>
                                </td>
                                <td class="py-4 text-slate-600">
                                    <?= date('d/m/Y', strtotime($comp->data_inicio)) ?>
                                    <?php if ($comp->data_fim !== $comp->data_inicio): ?>
                                        - <?= date('d/m/Y', strtotime($comp->data_fim)) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 text-center">
                                    <?php
                                    $statusClasses = [
                                        'rascunho' => 'bg-slate-100 text-slate-600',
                                        'aberta' => 'bg-green-50 text-green-700',
                                        'em_andamento' => 'bg-blue-50 text-blue-700',
                                        'encerrada' => 'bg-red-50 text-red-700',
                                    ];
                                    $statusLabels = [
                                        'rascunho' => 'Rascunho',
                                        'aberta' => 'Aberta',
                                        'em_andamento' => 'Em Andamento',
                                        'encerrada' => 'Encerrada',
                                    ];
                                    $classe = $statusClasses[$comp->status] ?? 'bg-slate-100 text-slate-600';
                                    $label = $statusLabels[$comp->status] ?? $comp->status;
                                    ?>
                                    <span class="px-2 py-1 rounded text-xs font-bold uppercase <?= $classe ?>">
                                        <?= $label ?>
                                    </span>
                                </td>
                                <td class="py-4 text-center text-slate-600">
                                    <?= count($comp->provas) ?>
                                </td>
                                <td class="py-4 text-center text-slate-600">
                                    <?= $comp->total_inscritos ?>
                                </td>
                                <td class="py-4 text-center">
                                    <?php if ($comp->total_resultados > 0): ?>
                                        <span class="text-green-600 font-semibold"><?= $comp->total_resultados ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?= route('admin.relatorios.competicao', ['id' => $comp->id]) ?>" 
                                           class="p-2 text-slate-500 hover:text-primary-600 hover:bg-primary-50 rounded transition-colors"
                                           title="Ver relatório">
                                            <i class="fa-solid fa-chart-line"></i>
                                        </a>
                                        <?php if ($comp->total_resultados > 0): ?>
                                            <a href="<?= route('admin.relatorios.competicao.csv', ['id' => $comp->id]) ?>" 
                                               class="p-2 text-slate-500 hover:text-green-600 hover:bg-green-50 rounded transition-colors"
                                               title="Exportar CSV">
                                                <i class="fa-solid fa-file-csv"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
