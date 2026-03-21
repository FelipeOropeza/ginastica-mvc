<?php $this->layout('layouts/judge', ['title' => $title]) ?>

<?php
$equipes = [];
$avaliados = 0;
foreach ($inscricoes as $ins) {
    if ($ins->atleta->equipe ?? null) {
        $equipes[$ins->atleta->equipe->id] = $ins->atleta->equipe->nome;
    }
    if ($ins->notaPorJurado) $avaliados++;
}
asort($equipes);
$total = count($inscricoes);
$pendentes = $total - $avaliados;
?>

<div class="flex items-center gap-4 mb-8">
    <a href="<?= route('juiz.dashboard') ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-600 hover:border-primary-200 transition-all shadow-sm">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div>
        <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Avaliação em Tempo Real</h2>
        <p class="text-2xl font-outfit font-black text-slate-800 tracking-tight leading-none"><?= e($title) ?></p>
    </div>
    
    <div class="ml-auto hidden md:flex items-center gap-3">
        <div class="text-right">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Seu Painel:</p>
            <p class="text-[11px] font-black text-primary-600 uppercase tracking-tighter leading-none border-b-2 border-primary-500 pb-0.5">
                <?= e(str_replace('nota_', '', $designacao->criterio)) ?>
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div class="lg:col-span-3">
        <!-- Barra de Progresso e Filtros -->
        <div class="card p-6 mb-6 bg-white shadow-xl shadow-slate-200/50 border-none relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Progresso da Prova</span>
                        <span class="text-[10px] font-black text-primary-600 uppercase tracking-widest"><?= $avaliados ?>/<?= $total ?> Avaliados</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-primary-500 rounded-full transition-all duration-700 ease-out" style="width: <?= $total > 0 ? ($avaliados/$total)*100 : 0 ?>%"></div>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 shrink-0">
                    <div class="text-center bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 min-w-[80px]">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter leading-none mb-1">Status</p>
                        <span class="text-[10px] font-black <?= $competicao->status === 'em_andamento' ? 'text-green-600' : 'text-red-500' ?> uppercase">
                            <?= $competicao->status === 'em_andamento' ? '● AO VIVO' : 'BLOQUEADO' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="searchAtleta" placeholder="Buscar ginasta..." 
                       class="w-full pl-11 pr-4 py-3 rounded-2xl border-none bg-white shadow-md focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm font-bold text-slate-700">
            </div>
            
            <div class="relative min-w-[220px]">
                <i class="fa-solid fa-filter absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs text-primary-500"></i>
                <select id="filterEquipe" 
                        class="w-full pl-11 pr-10 py-3 rounded-2xl border-none bg-white shadow-md focus:ring-2 focus:ring-primary-500/20 outline-none appearance-none transition-all text-sm font-bold text-slate-700 cursor-pointer">
                    <option value="">Equipes (Todas)</option>
                    <?php foreach ($equipes as $id => $nome): ?>
                        <option value="<?= e($nome) ?>"><?= e($nome) ?></option>
                    <?php endforeach; ?>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 text-[10px] pointer-events-none"></i>
            </div>
        </div>

        <!-- Lista de Atletas -->
        <div class="grid grid-cols-1 gap-4" id="atletas-list">
            <?php if (empty($inscricoes)): ?>
                <div class="card p-16 text-center border-dashed border-2 bg-slate-50/50">
                    <i class="fa-solid fa-users-slash text-slate-200 text-4xl mb-4"></i>
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-sm">Sem atletas para avaliação</p>
                </div>
            <?php else: ?>
                <?php foreach ($inscricoes as $index => $ins): ?>
                    <div class="card p-5 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 bg-white border-none group athlete-card" 
                         data-equipe="<?= e($ins->atleta->equipe->nome ?? '') ?>"
                         data-nome="<?= e(mb_strtolower($ins->atleta->nome_completo ?? '')) ?>">
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div class="flex-1 min-w-0 flex items-center gap-4">
                                <span class="w-8 h-8 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center text-xs font-black shadow-inner group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                                    <?= $index + 1 ?>
                                </span>
                                <div class="min-w-0">
                                    <h4 class="font-outfit font-black text-slate-800 text-lg leading-tight mb-1"><?= e($ins->atleta->nome_completo ?? 'Sem Nome') ?></h4>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] flex items-center gap-2">
                                        <i class="fa-solid fa-shield-halved text-slate-300"></i> <?= e($ins->atleta->equipe->nome ?? 'Avulso') ?>
                                    </p>
                                </div>
                            </div>

                            <div class="w-full md:w-auto shrink-0" id="nota-container-<?= $ins->id ?>">
                                <?php if ($ins->notaPorJurado): ?>
                                    <div class="bg-primary-50 rounded-2xl px-6 py-3 border border-primary-100 flex items-center gap-4 shadow-inner">
                                        <div class="text-right">
                                            <p class="text-[9px] font-black text-primary-400 uppercase tracking-widest leading-none mb-1">Nota Confirmada</p>
                                            <p class="text-[8px] text-primary-300 font-bold uppercase"><?= date('H:i:s', strtotime($ins->notaPorJurado->registrado_em)) ?></p>
                                        </div>
                                        <div class="text-2xl font-outfit font-black text-primary-600">
                                            <?= number_format($ins->notaPorJurado->valor, 3) ?>
                                        </div>
                                    </div>
                                <?php elseif ($competicao->status === 'em_andamento'): ?>
                                    <form hx-post="<?= route('juiz.salvar_nota', ['inscricao_id' => $ins->id]) ?>" 
                                          hx-target="#msg-<?= $ins->id ?>" 
                                          hx-swap="innerHTML"
                                          hx-on::after-request="this.classList.add('opacity-50', 'pointer-events-none'); this.querySelectorAll('input').forEach(i => i.disabled = true)"
                                          class="flex items-center gap-2">
                                        <?php 
                                            $label = 'Nota';
                                            $criterio = $designacao->criterio ?? 'geral';
                                            $max = 10;
                                            $hint = '0 a 10.0';
                                            
                                            if ($criterio === 'nota_d') {
                                                $label = 'Nota D';
                                                $max = 15;
                                                $hint = '0 a 15.0';
                                            } elseif ($criterio === 'nota_e') {
                                                $label = 'Nota E';
                                                $max = 10;
                                                $hint = '0 a 10.0';
                                            } elseif ($criterio === 'geral') {
                                                $label = 'Nota Geral';
                                                $max = 20;
                                                $hint = '0 a 20.0';
                                            }
                                        ?>
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="flex items-center gap-2">
                                                <div class="relative">
                                                    <input name="valor" type="number" step="0.001" min="0" max="<?= $max ?>" placeholder="0.000" required
                                                           class="w-32 px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all font-outfit font-black text-xl text-center text-slate-800">
                                                    <span class="absolute -top-1.5 left-3 px-2 bg-white text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none"><?= $label ?></span>
                                                </div>
                                                <button type="submit" class="w-12 h-12 bg-primary-600 text-white rounded-2xl shadow-lg shadow-primary-600/20 hover:bg-primary-700 hover:scale-110 active:scale-90 transition-all flex items-center justify-center group/send">
                                                    <i class="fa-solid fa-check text-lg group-hover/send:rotate-12"></i>
                                                </button>
                                            </div>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter italic">Sugerido: <?= $hint ?></span>
                                        </div>
                                    </form>
                                    <div id="msg-<?= $ins->id ?>" class="mt-1 text-center text-[10px] font-bold"></div>
                                <?php else: ?>
                                    <div class="px-6 py-3 rounded-2xl bg-slate-100 text-slate-400 text-[10px] font-black uppercase tracking-widest border border-slate-200 grayscale opacity-60">
                                        <i class="fa-solid fa-lock mr-2"></i> Bloqueado
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar de Comandos -->
    <div class="space-y-6">
        <div class="card p-6 bg-slate-900 border-none shadow-2xl relative overflow-hidden group">
            <h3 class="text-[10px] font-black text-primary-400 uppercase tracking-[0.2em] mb-4">Painel de Instruções</h3>
            <div class="space-y-5 relative z-10">
                <div class="p-3 bg-white/5 rounded-xl border border-white/10 hover:border-primary-500/50 transition-colors">
                    <p class="text-[10px] font-black text-white uppercase tracking-widest mb-1">Critério Atual</p>
                    <p class="text-xs text-slate-400 italic">Você está avaliando <span class="text-white font-bold"><?= e(str_replace('nota_', 'Nota ', $designacao->criterio)) ?></span>.</p>
                </div>
                <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                    <p class="text-[10px] font-black text-white uppercase tracking-widest mb-1">Cálculo de Prova</p>
                    <p class="text-xs text-slate-400 italic">Tipo: <span class="text-white font-bold"><?= e(str_replace('_', ' ', $prova->tipo_calculo)) ?></span>.</p>
                </div>
            </div>
            <i class="fa-solid fa-gavel absolute -right-6 -bottom-6 text-8xl text-white/5 -rotate-12 transition-transform duration-700 group-hover:rotate-0"></i>
        </div>

        <div class="card p-6 bg-primary-50 border-primary-100 shadow-sm relative overflow-hidden">
            <div class="flex items-start gap-3 relative z-10">
                <div class="w-8 h-8 rounded-lg bg-primary-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-primary-600/30">
                    <i class="fa-solid fa-keyboard text-xs"></i>
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-primary-900 uppercase tracking-widest mb-1">Atalhos</h4>
                    <p class="text-[11px] text-primary-700 font-medium leading-relaxed">
                        Use a tecla <span class="bg-white px-1 border border-primary-300 rounded text-[9px]">TAB</span> para alternar entre ginastas e <span class="bg-white px-1 border border-primary-300 rounded text-[9px]">ENTER</span> para enviar.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchAtleta');
    const equipeFilter = document.getElementById('filterEquipe');
    const cards = document.querySelectorAll('.athlete-card');

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
                card.style.animation = 'fadeIn 0.4s ease forwards';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterCards);
    equipeFilter.addEventListener('change', filterCards);
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
