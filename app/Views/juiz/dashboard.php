<?php $this->layout('layouts/judge', ['title' => $title]) ?>

<div class="mb-8">
    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Bem-vindo, Jurado</h2>
    <p class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Suas Designações Ativas</p>
</div>

<?php if (empty($competicoes)): ?>
    <div class="card p-12 text-center border-dashed border-2 bg-slate-50/30">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-300">
            <i class="fa-solid fa-calendar-xmark text-2xl"></i>
        </div>
        <h4 class="text-slate-600 font-bold mb-1">Nenhuma competição atribuída</h4>
        <p class="text-slate-400 text-xs italic mx-auto max-w-xs">Você ainda não foi designado para nenhuma prova. Entre em contato com a organização se houver algum erro.</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($competicoes as $comp): ?>
            <div class="card group hover:shadow-md transition-all border-l-4 border-l-amber-500">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-tighter">
                            <?= e($comp->status === 'em_andamento' ? 'Ao Vivo' : ($comp->status === 'aberta' ? 'Inscrições' : 'Finalizada')) ?>
                        </span>
                        <p class="text-[10px] font-bold text-slate-400 tracking-tighter"><?= date('d/m/Y', strtotime($comp->data_inicio)) ?></p>
                    </div>
                    
                    <h3 class="font-outfit font-bold text-slate-800 text-lg mb-4 group-hover:text-amber-600 transition-colors">
                        <?= e($comp->nome) ?>
                    </h3>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-slate-500 italic text-xs">
                            <i class="fa-solid fa-location-dot w-4"></i>
                            <span><?= e($comp->local) ?></span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3">Minhas Provas:</p>
                        <div class="space-y-2">
                            <?php 
                            // Como o controller passa as competições, precisamos das provas específicas desse jurado
                            // Vou buscar as designações aqui mesmo ou de forma simplificada
                            $juradoId = session('user')['id'];
                            $designacoes = (new \App\Models\JuradoDesignacao())
                                ->where('usuario_id', '=', $juradoId)
                                ->with(['prova'])
                                ->get();
                            
                            foreach ($designacoes as $des): 
                                if ($des->prova->competicao_id == $comp->id):
                            ?>
                                <a href="<?= route('juiz.avaliar', ['prova_id' => $des->prova->id]) ?>" 
                                   class="flex items-center justify-between p-2 rounded-lg bg-slate-50 hover:bg-amber-50 border border-slate-100 hover:border-amber-200 transition-all group/item">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-700 uppercase"><?= e(str_replace('_', ' ', $des->prova->aparelho)) ?></span>
                                        <span class="text-[9px] text-slate-400 uppercase tracking-tighter">Painel: <?= e(str_replace('nota_', '', $des->criterio)) ?></span>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover/item:text-amber-500 group-hover/item:translate-x-0.5 transition-all"></i>
                                </a>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
