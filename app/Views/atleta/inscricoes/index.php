<?php $this->layout('layouts/athlete', ['title' => $title]) ?>

<div class="mb-8">
    <h2 class="text-2xl font-outfit font-bold text-slate-800">Competições Abertas</h2>
    <p class="text-slate-500 text-sm">Confira as competições com inscrições abertas e participe.</p>
</div>

<?php if (empty($competicoes)): ?>
    <div class="card p-12 text-center border-dashed border-2 bg-slate-50/30">
        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-300">
            <i class="fa-solid fa-calendar-xmark text-3xl"></i>
        </div>
        <h3 class="text-slate-600 font-bold mb-1 font-outfit">Nenhuma competição aberta</h3>
        <p class="text-slate-400 text-sm mb-6 max-w-sm mx-auto">No momento não existem competições com inscrições abertas. Fique de olho no seu painel para novidades!</p>
        <a href="<?= route('atleta.dashboard') ?>" class="btn btn-primary px-8">Voltar ao Início</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($competicoes as $comp): ?>
            <div class="card group hover:shadow-xl hover:shadow-primary-600/5 transition-all duration-300 border-slate-200">
                <div class="h-32 bg-gradient-to-br from-primary-600 to-primary-900 relative">
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <i class="fa-solid fa-person-running text-8xl absolute -right-4 -bottom-4 rotate-12"></i>
                    </div>
                    <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-white text-[10px] font-bold uppercase tracking-wider">
                        Inscrições Abertas
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-calendar-day"></i>
                        </span>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                            <?= date('d/m/Y', strtotime($comp->data_inicio)) ?>
                        </p>
                    </div>
                    <h3 class="text-lg font-outfit font-bold text-slate-800 mb-2 truncate group-hover:text-primary-600 transition-colors">
                        <?= e($comp->nome) ?>
                    </h3>
                    <p class="text-slate-500 text-sm line-clamp-2 mb-6 leading-relaxed h-10">
                        <?= e($comp->descricao ?? 'Sem descrição informada.') ?>
                    </p>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-1.5 text-slate-400">
                            <i class="fa-solid fa-location-dot text-[10px]"></i>
                            <span class="text-[11px] font-medium"><?= e($comp->local ?? 'Local a definir') ?></span>
                        </div>
                        <a href="<?= route('atleta.competicoes.detalhes', ['id' => $comp->id]) ?>" class="btn bg-slate-900 text-white hover:bg-black px-4 py-2 text-xs">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
