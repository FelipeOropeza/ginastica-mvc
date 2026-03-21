<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="mb-6">
    <div class="flex items-center gap-3 mb-1">
        <a href="<?= route('admin.relatorios.index') ?>" class="text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-slate-900"><?= e($atleta->nome_completo) ?></h1>
    </div>
    <p class="text-sm text-slate-500 ml-10">
        <?php if ($atleta->cpf): ?>
            CPF: <?= e($atleta->cpf) ?> &bull;
        <?php endif; ?>
        <?php if ($atleta->numero_registro): ?>
            Registro: <?= e($atleta->numero_registro) ?>
        <?php endif; ?>
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="card p-5">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total de Inscrições</p>
        <p class="text-3xl font-bold text-slate-900"><?= count($atleta->inscricoes) ?></p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Competições</p>
        <p class="text-3xl font-bold text-slate-900">
            <?= count(array_unique(array_map(fn($i) => $i->competicao_id, $atleta->inscricoes))) ?>
        </p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Medalhas</p>
        <p class="text-3xl font-bold text-slate-900">
            <?php
            $medalhas = 0;
            foreach ($atleta->inscricoes as $inscricao) {
                if ($inscricao->resultado && $inscricao->resultado->podio) {
                    $medalhas++;
                }
            }
            echo $medalhas;
            ?>
        </p>
    </div>
</div>

<div class="card">
    <div class="p-5 border-b border-slate-100">
        <h2 class="text-lg font-semibold text-slate-800">Histórico de Participações</h2>
    </div>
    
    <div class="p-5">
        <?php if (empty($atleta->inscricoes)): ?>
            <div class="text-center py-12 text-slate-400">
                <i class="fa-solid fa-user-slash text-4xl mb-3"></i>
                <p>Nenhuma inscrição registrada.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b border-slate-100">
                            <th class="pb-3 font-medium">Competição</th>
                            <th class="pb-3 font-medium">Prova</th>
                            <th class="pb-3 font-medium">Data</th>
                            <th class="pb-3 font-medium text-center">Status</th>
                            <th class="pb-3 font-medium text-center">Class.</th>
                            <th class="pb-3 font-medium text-center">Nota Final</th>
                            <th class="pb-3 font-medium text-center">Podio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($atleta->inscricoes as $inscricao): ?>
                            <?php
                            $statusClass = match($inscricao->status) {
                                'confirmada' => 'bg-green-50 text-green-700',
                                'pendente' => 'bg-yellow-50 text-yellow-700',
                                'cancelada' => 'bg-red-50 text-red-700',
                                default => 'bg-slate-100 text-slate-600'
                            };
                            $podioIcon = match($inscricao->resultado?->podio) {
                                'ouro' => '<i class="fa-solid fa-medal text-yellow-500"></i>',
                                'prata' => '<i class="fa-solid fa-medal text-slate-400"></i>',
                                'bronze' => '<i class="fa-solid fa-medal text-orange-500"></i>',
                                default => '-'
                            };
                            ?>
                            <tr class="hover:bg-slate-50">
                                <td class="py-3">
                                    <p class="font-medium text-slate-800">
                                        <?= e($inscricao->competicao?->nome ?? 'N/A') ?>
                                    </p>
                                </td>
                                <td class="py-3 text-slate-600 capitalize">
                                    <?= str_replace('_', ' ', e($inscricao->prova?->aparelho ?? 'N/A')) ?>
                                </td>
                                <td class="py-3 text-slate-600">
                                    <?= date('d/m/Y', strtotime($inscricao->inscrito_em)) ?>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-1 rounded text-xs font-bold uppercase <?= $statusClass ?>">
                                        <?= $inscricao->status ?>
                                    </span>
                                </td>
                                <td class="py-3 text-center font-semibold">
                                    <?= $inscricao->resultado?->classificacao ? $inscricao->resultado->classificacao . 'º' : '-' ?>
                                </td>
                                <td class="py-3 text-center font-mono">
                                    <?= $inscricao->resultado?->nota_final ? number_format($inscricao->resultado->nota_final, 3) : '-' ?>
                                </td>
                                <td class="py-3 text-center">
                                    <?= $podioIcon ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
