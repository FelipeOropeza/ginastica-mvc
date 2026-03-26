<?php $this->layout('layouts/judge', ['title' => $title]) ?>

<?php
$avaliados = 0;
foreach ($inscricoes as $ins) {
    if ($ins->notaPorJurado) $avaliados++;
}
$total = count($inscricoes);
$porcentagem = $total > 0 ? ($avaliados / $total) * 100 : 0;
$porcentagem = $total > 0 ? ($avaliados / $total) * 100 : 0;
?>

<!-- Progress Bar OOB -->
<div id="progress-container" hx-swap-oob="true" class="card p-4 mb-6 bg-white border-none shadow-sm hx-target-fade">
    <div class="flex items-center justify-between mb-2">
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Progresso da Avaliação</span>
        <span class="text-[10px] font-black text-primary-600 uppercase tracking-widest"><?= $avaliados ?> / <?= $total ?></span>
<!-- Progress Bar OOB -->
<div id="progress-container" hx-swap-oob="true" class="card p-4 mb-6 bg-white border-none shadow-sm">
    <div class="flex items-center justify-between mb-2">
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Progresso da Avaliação</span>
        <span class="text-[10px] font-black text-primary-600 uppercase tracking-widest"><?= $avaliados ?> / <?= $total ?></span>
    </div>
    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full bg-primary-500 rounded-full transition-all duration-500" style="width: <?= $porcentagem ?>%"></div>
    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full bg-primary-500 rounded-full transition-all duration-500" style="width: <?= $porcentagem ?>%"></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-in fade-in duration-500">
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Header de Informações da Prova -->
        <div class="card bg-slate-50 border-none shadow-sm p-5 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-primary-600 border border-slate-100">
                    <i class="fa-solid fa-person-gymnastics text-xl"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block leading-none mb-1">Aparelho / Prova</span>
                    <h2 class="text-xl font-outfit font-black text-slate-800 capitalize leading-tight">
                        <?= str_replace('_', ' ', e($prova->aparelho)) ?>
                    </h2>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-6">
                <div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Sua Função</span>
                    <span class="px-3 py-1 rounded-lg bg-primary-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md shadow-primary-600/20">
                        <?= e(str_replace(['nota_d', 'nota_e', 'geral'], ['Nota D', 'Nota E', 'Avaliação Geral'], $designacao->criterio)) ?>
                    </span>
                </div>
                
                <div class="hidden sm:block">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Sistema</span>
                    <span class="px-3 py-1 rounded-lg bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest">
                        <?= $prova->tipo_calculo === 'nota_d_mais_e' ? 'Sistema FIG' : ($prova->tipo_calculo === 'media_sem_extremos' ? 'Média Olímpica' : 'Média Aritmética') ?>
                    </span>
                </div>

                <div class="text-right">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Ginastas</span>
                    <p class="text-lg font-outfit font-black text-slate-800 leading-none"><?= count($inscricoes) ?></p>
                </div>
            </div>
        </div>

        <!-- Área Central de Avaliação -->
        <div id="evaluation-center" 
             class="relative min-h-[400px]"
             <?= $jaAvaliouAtivo ? 'hx-get="'.route('juiz.avaliar', ['prova_id' => $prova->id]).'" hx-trigger="every 5s" hx-select="#evaluation-center" hx-swap="outerHTML"' : '' ?>>
            
            <?php if ($atletaAtivo): ?>
                <?php 
                    $currentIns = null;
                    foreach ($inscricoes as $ins) {
                        if ($ins->id === $atletaAtivo->id) {
                            $currentIns = $ins;
                            break;
                        }
                    }
                ?>
                <div class="card p-6 md:p-10 bg-white border-none shadow-xl shadow-slate-200/60 transition-all duration-500 overflow-hidden group">
                    <div class="absolute top-0 left-0 w-2 h-full bg-primary-600"></div>
                    
                    <div class="flex flex-col gap-8">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <span class="text-[11px] font-black text-primary-600 uppercase tracking-widest block mb-2">Ginasta Ativo</span>
                                <h3 class="text-3xl md:text-5xl font-outfit font-black text-slate-800 tracking-tight leading-tight">
                                    <?= e($currentIns->atleta->nome_completo) ?>
                                </h3>
                                <div class="flex items-center gap-3 mt-4">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider"><?= e($currentIns->atleta->equipe->nome ?? 'Avulso') ?></span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider text-primary-500"><?= e($currentIns->atleta->categoria->nome ?? 'Categoria') ?></span>
                                </div>
                            </div>
                            <div class="w-16 h-16 md:w-24 md:h-24 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-2xl md:text-4xl font-black shadow-lg">
                                <?= $currentIns->ordem_apresentacao ?: '#' ?>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-100">
                            <?php if ($jaAvaliouAtivo): ?>
                                <div class="flex flex-col items-center py-6 text-center animate-in fade-in slide-in-from-bottom-4 duration-500">
                                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center text-2xl mb-4">
                                        <i class="fa-solid fa-check-double"></i>
                                    </div>
                                    <h4 class="text-lg font-outfit font-black text-slate-800 mb-1">Nota Registrada</h4>
                                    <?php 
                                        $votoTotal = 0;
                                        foreach($currentIns->notas as $n) {
                                            if ($n->jurado_id == session('user')['id']) {
                                                $votoTotal += $n->valor;
                                            }
                                        }
                                    ?>
                                    <p class="text-3xl font-outfit font-black text-emerald-600 mb-4"><?= number_format($votoTotal, 3) ?></p>
                                    <div class="px-6 py-2 rounded-full bg-slate-50 border border-slate-200 flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Aguardando outros juízes...</span>
                                    </div>
                                </div>
                            <?php elseif ($competicao->status === 'em_andamento'): ?>
                                <form hx-post="<?= route('juiz.salvar_nota', ['inscricao_id' => $currentIns->id]) ?>" 
                                      hx-target="#evaluation-center" 
                                      hx-select="#evaluation-center"
                                      hx-swap="outerHTML"
                                      hx-disabled-elt="#btn-submit-nota"
                                      class="flex flex-col md:flex-row items-stretch gap-4">
                                    
                                    <?= csrf_field() ?>
                                    
                                    <?php 
                                        $isFig = $prova->tipo_calculo === 'nota_d_mais_e';
                                        $isGeral = $designacao->criterio === 'geral';
                                    ?>
                                    
                                    <?php if ($isFig && $isGeral): ?>
                                        <div class="grid grid-cols-2 gap-4 flex-1">
                                            <div class="relative">
                                                <input name="nota_d" type="number" step="0.001" min="0" max="20" placeholder="0.000" autofocus required id="input-nota-d"
                                                       class="w-full h-[80px] px-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/5 outline-none transition-all font-outfit font-black text-2xl text-center text-slate-800 placeholder:text-slate-200">
                                                <div class="absolute -top-3 left-6">
                                                    <label class="px-2 bg-blue-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-md leading-loose">Nota D (Dificuldade)</label>
                                                </div>
                                            </div>
                                            <div class="relative">
                                                <input name="nota_e" type="number" step="0.001" min="0" max="10" placeholder="0.000" required id="input-nota-e"
                                                       class="w-full h-[80px] px-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/5 outline-none transition-all font-outfit font-black text-2xl text-center text-slate-800 placeholder:text-slate-200">
                                                <div class="absolute -top-3 left-6">
                                                    <label class="px-2 bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-md leading-loose">Nota E (Execução)</label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="relative flex-1">
                                            <?php 
                                                $max = ($designacao->criterio === 'nota_d') ? 20 : 10;
                                                $label = str_replace(['nota_d', 'nota_e', 'geral'], ['Nota D', 'Nota E', 'Avaliação Geral'], $designacao->criterio);
                                                $color = $designacao->criterio === 'nota_d' ? 'bg-blue-600' : ($designacao->criterio === 'nota_e' ? 'bg-emerald-600' : 'bg-primary-600');
                                            ?>
                                            <input name="valor" type="number" step="0.001" min="0" max="<?= $max ?>" placeholder="0.000" autofocus required id="input-nota"
                                                   class="w-full h-full min-h-[80px] px-8 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/5 outline-none transition-all font-outfit font-black text-4xl text-center text-slate-800 placeholder:text-slate-200">
                                            
                                            <div class="absolute -top-3 left-6 flex gap-2 child-inline">
                                                <label class="px-2 <?= $color ?> text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-md leading-loose">
                                                    <?= e($label) ?>
                                                </label>
                                                <span class="px-2 bg-slate-800 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-md leading-loose">
                                                    Max: <?= number_format($max, 1) ?>
                                                </span>
                                            </div>
                                            <p class="text-center mt-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest">Insira o valor da sua avaliação</p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <button type="submit" id="btn-submit-nota" class="md:w-48 bg-primary-600 text-white rounded-2xl shadow-lg shadow-primary-600/20 hover:bg-primary-700 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex flex-col items-center justify-center gap-1.5 py-4 group">
                                        <i class="fa-solid fa-check-to-slot text-xl group-hover:-translate-y-1 transition-transform group-disabled:hidden"></i>
                                        <i class="fa-solid fa-spinner fa-spin hidden group-disabled:block"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Confirmar Nota</span>
                                        <span class="text-[8px] font-medium opacity-50">Clique ou Enter</span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="p-8 rounded-3xl bg-slate-50 text-slate-400 text-center border-2 border-dashed border-slate-200">
                                    <i class="fa-solid fa-pause-circle text-2xl mb-2"></i>
                                    <p class="text-xs font-black uppercase tracking-widest">Competição Bloqueada</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card p-16 text-center border-none bg-white shadow-xl shadow-slate-200/50 rounded-3xl">
                    <div class="w-20 h-20 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <h3 class="text-2xl font-outfit font-black text-slate-800 mb-2">Rodada Finalizada</h3>
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-xs leading-relaxed">Parabéns! Todos os ginastas desta bateria foram avaliados.</p>
                    <a href="<?= route('juiz.dashboard') ?>" class="mt-8 inline-flex items-center gap-3 bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold text-xs uppercase hover:bg-slate-800 transition-all shadow-lg active:scale-95">
                        <i class="fa-solid fa-home"></i> Painel Inicial
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="space-y-4">
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Fila de Apresentação</h3>
        <div id="atletas-list" class="space-y-2">
            <?php foreach ($inscricoes as $ins): ?>
                <?php 
                    $isAtivo = $atletaAtivo && $ins->id === $atletaAtivo->id;
                    $jaVotou = (bool) $ins->notaPorJurado;
                ?>
                <div class="card p-3 border-none transition-all duration-300 <?= $isAtivo ? 'bg-primary-600 text-white shadow-lg -translate-x-2' : ($jaVotou ? 'bg-emerald-50/50 opacity-60' : 'bg-white') ?>">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[11px] font-black <?= $isAtivo ? 'bg-white/20' : ($jaVotou ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400') ?>">
                            <?php if ($jaVotou): ?>
                                <i class="fa-solid fa-check"></i>
                            <?php else: ?>
                                <?= $ins->ordem_apresentacao ?: '#' ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-xs truncate <?= $isAtivo ? 'text-white' : 'text-slate-800' ?>">
                                <?= e($ins->atleta->nome_completo) ?>
                            </h4>
                            <p class="text-[9px] font-bold uppercase tracking-tight opacity-60">
                                <?= e($ins->atleta->equipe->nome ?? 'Avulso') ?>
                            </p>
                        </div>

                        <?php if ($jaVotou): ?>
                            <?php 
                                $votoSoma = 0;
                                foreach($ins->notas as $n) {
                                    if ($n->jurado_id == session('user')['id']) $votoSoma += $n->valor;
                                }
                            ?>
                            <span class="text-[10px] font-black font-outfit <?= $isAtivo ? 'text-white' : 'text-slate-600' ?>">
                                <?= number_format($votoSoma, 3) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card p-4 bg-slate-900 text-white border-none shadow-xl mt-6">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-keyboard text-primary-400 mt-0.5"></i>
        <div class="card p-4 bg-slate-900 text-white border-none shadow-xl mt-6">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-keyboard text-primary-400 mt-0.5"></i>
                <div>
                    <h5 class="text-[9px] font-black text-primary-400 uppercase tracking-widest mb-1">Dica de Produtividade</h5>
                    <p class="text-[11px] text-slate-400 leading-snug">O cursor foca automaticamente no campo de nota. Após digitar, aperte <kbd class="px-1 bg-white/10 rounded text-white inline-block">Enter</kbd> para enviar rápido.</p>
                    <h5 class="text-[9px] font-black text-primary-400 uppercase tracking-widest mb-1">Dica de Produtividade</h5>
                    <p class="text-[11px] text-slate-400 leading-snug">O cursor foca automaticamente no campo de nota. Após digitar, aperte <kbd class="px-1 bg-white/10 rounded text-white inline-block">Enter</kbd> para enviar rápido.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setupFocus() {
        const input = document.getElementById('input-nota') || document.getElementById('input-nota-d');
        if (input) {
            input.focus();
            input.select();
        }
    }

    // Executa no load inicial
    document.addEventListener('DOMContentLoaded', setupFocus);

    // Executa após trocas do HTMX
    document.body.addEventListener('htmx:afterSwap', (evt) => {
        if (evt.detail.target.id === 'evaluation-center') {
            setupFocus();
        }
    });

    // Atalho de teclado para focar (Esc ou f)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') setupFocus();
    });
</script>
