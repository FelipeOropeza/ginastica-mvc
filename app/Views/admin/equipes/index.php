<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Gestão de Equipes</h2>
        <p class="text-sm text-slate-500">Cadastre clubes e associações esportivas.</p>
    </div>
    <a href="<?= route('admin.equipes.create') ?>" class="btn btn-primary gap-2 w-fit">
        <i class="fa-solid fa-plus text-xs"></i> Nova Equipe
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($equipes)): ?>
        <div class="col-span-full card p-12 text-center border-dashed border-2">
            <i class="fa-solid fa-users-viewfinder text-3xl text-slate-300 mb-3 block"></i>
            <p class="text-slate-400">Nenhuma equipe cadastrada ainda.</p>
        </div>
    <?php else: ?>
        <?php foreach ($equipes as $equipe): ?>
            <div class="card group hover:border-primary-300 transition-all">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                            <i class="fa-solid fa-shield-halved text-xl"></i>
                        </div>
                        <div class="flex gap-1">
                            <a href="<?= route('admin.equipes.edit', ['id' => $equipe->id]) ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-amber-600 transition-all">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>
                        </div>
                    </div>
                    
                    <h3 class="font-bold text-slate-800 mb-1"><?= e($equipe->nome) ?></h3>
                    <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-4">
                        <i class="fa-solid fa-location-dot mr-1"></i> <?= e($equipe->cidade ?? 'Cidade não info.') ?> / <?= e($equipe->estado ?? '--') ?>
                    </p>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <span class="text-[9px] font-bold text-slate-500 uppercase">Cores: <?= e($equipe->cores ?? 'N/A') ?></span>
                        <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase <?= $equipe->ativo ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500' ?>">
                            <?= $equipe->ativo ? 'Ativa' : 'Inativa' ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
