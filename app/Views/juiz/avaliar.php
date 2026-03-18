<?php $this->layout('layouts/judge', ['title' => $title]) ?>

<?php
$equipes = [];
foreach ($inscricoes as $ins) {
    if ($ins->atleta->equipe ?? null) {
        $equipes[$ins->atleta->equipe->id] = $ins->atleta->equipe->nome;
    }
}
asort($equipes);
?>

<div class="flex items-center gap-3 mb-8">
    <a href="<?= route('juiz.dashboard') ?>" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-white hover:text-slate-600 transition-all shadow-sm">
        <i class="fa-solid fa-arrow-left text-xs"></i>
    </a>
    <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">
        <?= e($title) ?>
    </h2>
    <div class="ml-auto flex items-center gap-3">
        <div class="text-right">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Painel:</p>
            <p class="text-[11px] font-black text-amber-600 uppercase tracking-tighter leading-none"><?= e(str_replace('nota_', '', $designacao->criterio)) ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div class="lg:col-span-3 space-y-6">
        <div class="card bg-slate-100/50 border-none p-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 shadow-sm">
                    <i class="fa-solid fa-person-gymnastics text-base"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm"><?= e($prova->categoria->nome ?? 'Categoria Geral') ?></h3>
                    <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest truncate"><?= e($competicao->nome) ?></p>
                </div>
            </div>
            
            <div class="flex items-center gap-6 pr-4">
                <div class="text-center">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Atletas</p>
                    <p class="text-lg font-outfit font-black text-slate-700 leading-none"><?= count($inscricoes) ?></p>
                </div>
                <div class="text-center">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Status</p>
                    <span class="px-2 py-0.5 rounded <?= $competicao->status === 'em_andamento' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> text-[9px] font-black uppercase tracking-tighter shadow-sm border border-transparent">
                        <?= e($competicao->status === 'em_andamento' ? 'Ao Vivo' : 'Bloqueado') ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="mb-6 flex flex-wrap gap-4 items-center">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="searchAtleta" placeholder="Buscar atleta pelo nome..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-slate-100 focus:border-amber-500 outline-none transition-all text-sm shadow-sm bg-white">
            </div>
            
            <div class="relative min-w-[200px]">
                <i class="fa-solid fa-filter absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <select id="filterEquipe" 
                        class="w-full pl-10 pr-10 py-2.5 rounded-xl border-2 border-slate-100 focus:border-amber-500 outline-none appearance-none transition-all text-sm shadow-sm bg-white cursor-pointer">
                    <option value="">Todos os Clubes / Equipes</option>
                    <?php foreach ($equipes as $id => $nome): ?>
                        <option value="<?= e($nome) ?>"><?= e($nome) ?></option>
                    <?php endforeach; ?>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>
        </div>

        <div class="space-y-4" id="atletas-list">
            <?php if (empty($inscricoes)): ?>
                <div class="card p-12 text-center border-dashed border-2 bg-slate-50/30">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <i class="fa-solid fa-ghost text-2xl"></i>
                    </div>
                    <h4 class="text-slate-600 font-bold mb-1">Nenhum atleta inscrito nesta prova</h4>
                    <p class="text-slate-400 text-xs italic mx-auto max-w-xs">Assim que houver atletas confirmados, eles aparecerão aqui para sua avaliação.</p>
                </div>
            <?php else: ?>
                <?php foreach ($inscricoes as $ins): ?>
                    <div class="card p-6 flex flex-col md:flex-row items-center gap-6 group hover:border-amber-400 transition-all border-l-4 border-l-slate-200 atleta-card" 
                         data-equipe="<?= e($ins->atleta->equipe->nome ?? '') ?>"
                         data-nome="<?= e(mb_strtolower($ins->atleta->nome ?? '')) ?>">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black font-outfit text-slate-500 shadow-inner">
                                    <?= e($ins->ordem_apresentacao ?? 0) ?>
                                </span>
                                <h4 class="font-outfit font-bold text-slate-800 text-lg group-hover:text-amber-600 transition-all truncate atleta-nome">
                                    <?= e($ins->atleta->nome ?? 'Atleta...') ?>
                                </h4>
                            </div>
                            <p class="text-xs text-slate-500 italic ml-8 leading-none">
                                <span class="font-bold uppercase text-[10px] not-italic mr-1 opacity-60">Equipe:</span> <?= e($ins->atleta->equipe->nome ?? '...') ?>
                            </p>
                        </div>

                        <div class="flex items-center gap-4 w-full md:w-auto shrink-0" id="nota-container-<?= $ins->id ?>">
                            <?php if ($ins->notaPorJurado): ?>
                                <div class="flex flex-col items-center gap-1 min-w-[120px]">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Nota Enviada</p>
                                    <div class="bg-green-50 text-green-700 px-4 py-1.5 rounded-full border border-green-200 flex items-center justify-center font-outfit font-black text-xl shadow-sm">
                                        <?= number_format($ins->notaPorJurado->valor, 3) ?>
                                    </div>
                                    <p class="text-[8px] font-bold text-slate-400 tracking-tighter uppercase"><?= date('H:i:s', strtotime($ins->notaPorJurado->registrado_em)) ?></p>
                                </div>
                            <?php elseif ($competicao->status === 'em_andamento'): ?>
                                <form hx-post="<?= route('juiz.salvar_nota', ['inscricao_id' => $ins->id]) ?>" 
                                      hx-target="#msg-<?= $ins->id ?>" 
                                      hx-swap="innerHTML"
                                      hx-on::after-request="this.remove()"
                                      class="flex items-center gap-3 w-full md:w-auto">
                                    <div class="space-y-1">
                                        <input name="valor" type="number" step="0.001" min="0" max="25" placeholder="0.000" required autofocus
                                               class="w-24 px-4 py-2 text-center rounded-lg border-2 border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 font-outfit font-bold text-lg outline-none transition-all placeholder:text-slate-300">
                                    </div>
                                    <button type="submit" class="w-12 h-12 rounded-xl bg-amber-600 text-white hover:bg-amber-700 hover:scale-105 active:scale-95 transition-all shadow-lg shadow-amber-600/20 flex items-center justify-center group/btn">
                                        <i class="fa-solid fa-paper-plane text-base group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5 transition-all"></i>
                                    </button>
                                </form>
                                <div id="msg-<?= $ins->id ?>"></div>
                            <?php else: ?>
                                <span class="bg-slate-100 text-slate-400 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest border border-slate-200 shadow-sm cursor-not-allowed grayscale">
                                    <i class="fa-solid fa-lock mr-1.5 text-[10px]"></i> Bloqueado
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar Informativa -->
    <div class="space-y-6">
        <div class="card p-6 bg-slate-900 text-white relative overflow-hidden border-none shadow-xl">
            <h3 class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-4">Lembrete Técnico</h3>
            <div class="space-y-4 relative z-10">
                <div class="text-xs space-y-2">
                    <p class="font-bold border-l-2 border-amber-500 pl-3">Painel E (Execução):</p>
                    <p class="text-slate-400 opacity-80 pl-3 italic">Deduções de faltas técnicas. O valor final deve refletir o desempenho do atleta.</p>
                </div>
                <div class="text-xs space-y-2">
                    <p class="font-bold border-l-2 border-amber-600 pl-3">Painel D (Dificuldade):</p>
                    <p class="text-slate-400 opacity-80 pl-3 italic">Soma do valor das figuras, ligações e requisitos de composição.</p>
                </div>
            </div>
            <i class="fa-solid fa-award absolute -right-4 -bottom-4 text-7xl text-white/5 opacity-50 -rotate-12"></i>
        </div>

        <div class="card p-6 bg-amber-50 border-amber-200 shadow-sm">
            <h4 class="text-xs font-bold text-amber-900 uppercase tracking-widest mb-3 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-amber-600"></i> Dica do Sistema
            </h4>
            <p class="text-[11px] text-amber-800 leading-relaxed font-medium">
                Pressione <kbd class="px-1.5 py-0.5 rounded bg-white text-xs border border-amber-300 shadow-sm">Enter</kbd> para enviar a nota rapidamente. 
                O sistema recalcula a nota final do atleta automaticamente assim que o painel for completo.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchAtleta');
    const equipeFilter = document.getElementById('filterEquipe');
    const cards = document.querySelectorAll('.atleta-card');

    function filterCards() {
        const searchTerm = searchInput.value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        const selectedEquipe = equipeFilter.value;

        cards.forEach(card => {
            const atletaNome = card.dataset.nome.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            const equipeNome = card.dataset.equipe;

            const matchesSearch = atletaNome.includes(searchTerm);
            const matchesEquipe = selectedEquipe === "" || equipeNome === selectedEquipe;

            if (matchesSearch && matchesEquipe) {
                card.style.display = '';
                card.classList.add('animate-in');
            } else {
                card.style.display = 'none';
                card.classList.remove('animate-in');
            }
        });
    }

    searchInput.addEventListener('input', filterCards);
    equipeFilter.addEventListener('change', filterCards);
});
</script>

<style>
.animate-in {
    animation: slideIn 0.3s ease-out forwards;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
