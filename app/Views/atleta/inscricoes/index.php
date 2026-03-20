<?php $this->layout('layouts/athlete', ['title' => $title]) ?>

<div class="mb-8">
    <h2 class="text-2xl font-outfit font-black text-slate-800 tracking-tight">Calendário de Competições</h2>
    <p class="text-sm text-slate-500">Veja as competições abertas e filtre por data ou local.</p>
</div>

<?php if (empty($competicoes)): ?>
    <div class="card p-12 text-center border-dashed border-2 bg-slate-50/50">
        <i class="fa-solid fa-calendar-xmark text-4xl text-slate-200 mb-4 mx-auto"></i>
        <p class="text-slate-400 font-bold uppercase tracking-widest text-sm">Nenhuma competição encontrada</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($competicoes as $comp): ?>
            <div class="bg-white shadow-sm border border-slate-200 rounded-2xl flex flex-col overflow-hidden">
                <!-- Status Header -->
                <?php 
                    $statMap = [
                        'aberta' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Inscrições Abertas'],
                        'em_andamento' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Em Andamento'],
                        'encerrada' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'label' => 'Finalizada'],
                    ];
                    $st = $statMap[$comp->status] ?? $statMap['encerrada'];
                ?>
                <div class="p-6 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest <?= $st['bg'] ?> <?= $st['text'] ?>">
                            <?= $st['label'] ?>
                        </span>
                        <p class="text-[10px] text-slate-400 font-bold"><?= date('d/m/Y', strtotime($comp->data_inicio)) ?></p>
                    </div>

                    <h3 class="text-lg font-outfit font-black text-slate-800 mb-2 group-hover:text-primary-600 transition-colors">
                        <?= e($comp->nome) ?>
                    </h3>
                    
                    <p class="text-xs text-slate-500 flex items-center gap-2 mb-6">
                        <i class="fa-solid fa-location-dot text-slate-300"></i> <?= e($comp->local ?? 'Local Indefinido') ?>
                    </p>

                    <div class="mt-auto">
                        <?php if ($comp->status === 'aberta'): ?>
                            <button hx-get="<?= route('atleta.competicoes.form', ['id' => $comp->id]) ?>" 
                                    hx-target="#modal-container" 
                                    hx-swap="innerHTML"
                                    class="w-full btn btn-primary py-3 text-xs font-black uppercase tracking-[0.1em] rounded-xl shadow-lg shadow-primary-600/20">
                                Inscrever-se <i class="fa-solid fa-arrow-right ml-1 opacity-50 text-[10px]"></i>
                            </button>
                        <?php else: ?>
                            <div class="w-full bg-slate-50 text-slate-400 py-3 text-[10px] font-black uppercase tracking-widest rounded-xl border border-slate-100 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-lock text-[10px]"></i> Indisponível
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div id="modal-container"></div>
