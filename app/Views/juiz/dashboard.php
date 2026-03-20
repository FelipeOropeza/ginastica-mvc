<?php $this->layout('layouts/judge', ['title' => $title]) ?>

<div class="mb-8 p-6 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-xs font-bold text-primary-500 uppercase tracking-widest mb-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-primary-500 animate-pulse"></span> Painel Oficial
        </h2>
        <p class="text-3xl font-outfit font-black text-slate-800 tracking-tight">Suas Designações</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="text-right">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Logado como:</p>
            <p class="text-xs font-black text-slate-600 uppercase tracking-tight leading-none"><?= e(session('user')['nome']) ?></p>
        </div>
        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
            <i class="fa-solid fa-user-tie"></i>
        </div>
    </div>
</div>

<?php if (empty($competicoes)): ?>
    <div class="card p-12 text-center border-dashed border-2 bg-slate-50/30">
        <div class="w-20 h-20 rounded-full bg-white shadow-sm flex items-center justify-center mx-auto mb-6 text-slate-300">
            <i class="fa-solid fa-calendar-xmark text-3xl"></i>
        </div>
        <h4 class="text-slate-700 font-bold text-lg mb-2">Nenhuma competição atribuída</h4>
        <p class="text-slate-500 text-sm italic mx-auto max-w-sm">
            Você ainda não foi designado para nenhuma prova ou aparelho. 
            Contate o administrador para verificar sua escala.
        </p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($competicoes as $comp): ?>
            <div class="card group hover:shadow-xl transition-all duration-500 border-none bg-white relative overflow-hidden flex flex-col">
                <!-- Barra de Status Dinâmica -->
                <?php 
                    $statMap = [
                        'rascunho' => ['bg' => 'bg-slate-200', 'text' => 'text-slate-600', 'label' => 'Rascunho'],
                        'aberta' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'Aguardando Atletas'],
                        'em_andamento' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Em Andamento / No Ar'],
                        'encerrada' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Finalizada'],
                    ];
                    $st = $statMap[$comp->status] ?? $statMap['rascunho'];
                ?>
                <div class="absolute top-0 left-0 w-full h-1.5 <?= $st['bg'] ?>"></div>
                
                <div class="p-6 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2 py-0.5 rounded-full <?= $st['bg'] ?> <?= $st['text'] ?> text-[8px] font-black uppercase tracking-widest border border-black/5">
                            <?= $st['label'] ?>
                        </span>
                        <p class="text-[10px] font-bold text-slate-400 tracking-tighter italic">
                            <?= date('d M Y', strtotime($comp->data_inicio)) ?>
                        </p>
                    </div>
                    
                    <h3 class="font-outfit font-bold text-slate-800 text-xl mb-1 group-hover:text-primary-600 transition-colors leading-tight">
                        <?= e($comp->nome) ?>
                    </h3>
                    <p class="text-[11px] text-slate-500 mb-6 flex items-center gap-1">
                        <i class="fa-solid fa-location-dot text-primary-500 opacity-60"></i> <?= e($comp->local) ?>
                    </p>

                    <div class="pt-5 border-t border-slate-50 mt-auto">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] mb-4">Minhas ProvasDesignadas</p>
                        <div class="space-y-3">
                            <?php foreach ($comp->minhas_provas as $des): ?>
                                <a href="<?= route('juiz.avaliar', ['prova_id' => $des->prova->id]) ?>" 
                                   class="relative flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-primary-50 border border-slate-100 hover:border-primary-200 transition-all group/btn overflow-hidden">
                                    <div class="flex flex-col relative z-10">
                                        <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight"><?= e(str_replace('_', ' ', $des->prova->aparelho)) ?></span>
                                        <span class="text-[10px] text-slate-500 italic flex items-center gap-2">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-primary-400"></span>
                                            <?= e($des->prova->categoria->nome ?? 'Categoria Geral') ?>
                                        </span>
                                    </div>
                                    <div class="bg-primary-600 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-tighter shadow-sm group-hover/btn:scale-105 transition-all">
                                        Avaliar
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Efeito Visual de Fundo -->
                <i class="fa-solid fa-person-gymnastics absolute -right-4 -bottom-4 text-7xl text-slate-100/50 -rotate-12 pointer-events-none group-hover:text-primary-500/10 transition-colors duration-500"></i>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
