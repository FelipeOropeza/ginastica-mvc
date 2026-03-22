<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex items-center gap-3 mb-6">
    <a href="/admin/competicoes/<?= $prova->competicao_id ?>/provas" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-white hover:text-slate-600 transition-all">
        <i class="fa-solid fa-arrow-left text-xs"></i>
    </a>
    <div>
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Banca de Jurados</h2>
        <p class="text-xs text-slate-500 font-medium tracking-tight uppercase">
            <?= str_replace('_', ' ', $prova->aparelho) ?> - 
            <?php 
                $cat = (new \App\Models\Categoria())->find($prova->categoria_id);
                echo $cat ? $cat->nome : 'N/A';
            ?>
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Form Designação -->
    <div class="lg:col-span-1">
        <div class="card p-5 h-fit sticky top-24">
            <h3 class="text-xs font-bold text-slate-900 mb-4 flex items-center gap-2 uppercase tracking-wider">
                <i class="fa-solid fa-user-plus text-primary-600"></i> Nova Designação
            </h3>
            
            <form action="<?= route('admin.provas.designacoes.store', ['id' => $prova->id]) ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                
                <?php 
                    $hasGeral = false;
                    foreach($designacoes as $d) if($d->criterio === 'geral') $hasGeral = true;
                ?>

                <?php if ($hasGeral): ?>
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg flex gap-3 mb-4">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                        <p class="text-[10px] text-amber-800 font-bold leading-tight">
                            Esta prova já possui um juiz <span class="uppercase">GERAL</span>. Nenhuma outra designação é permitida.
                        </p>
                    </div>
                <?php endif; ?>

                <div class="<?= $hasGeral ? 'opacity-50 pointer-events-none' : '' ?>">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Jurado</label>
                    <select name="usuario_id" class="form-input" required>
                        <option value="">Selecione um juiz...</option>
                        <?php foreach ($jurados as $jurado): ?>
                            <option value="<?= $jurado->id ?>"><?= e($jurado->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="<?= $hasGeral ? 'opacity-50 pointer-events-none' : '' ?>">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Função na Banca (Atribuição)</label>
                    <select name="criterio" class="form-input" required>
                        <?php foreach ($criterios as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $key === 'geral' ? 'class="font-black text-primary-600"' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[9px] text-slate-400 mt-1 italic leading-tight">
                        * Selecione 'Geral' para Bancas Únicas, Médias Aritméticas ou Olímpicas (onde todos dão a mesma nota).
                    </p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn btn-primary w-full shadow-lg shadow-primary-500/20" <?= $hasGeral ? 'disabled' : '' ?>>
                        Designar Juiz
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-200 flex flex-col gap-3">
            <div class="flex gap-3">
                <i class="fa-solid fa-circle-info text-primary-500 mt-0.5 text-sm"></i>
                <h4 class="text-[11px] font-black uppercase tracking-widest text-slate-700">Regras de Banca</h4>
            </div>
            <ul class="space-y-2">
                <li class="flex gap-2 text-[10px] text-slate-600">
                    <i class="fa-solid fa-check-circle text-emerald-500 mt-0.5"></i>
                    <span><strong>Nota D/E:</strong> Use para bancas técnicas separadas (Padrão FIG).</span>
                </li>
                <li class="flex gap-2 text-[10px] text-slate-600">
                    <i class="fa-solid fa-circle-exclamation text-amber-500 mt-0.5"></i>
                    <span><strong>Geral:</strong> Se escolher Geral, este juiz dará a nota final sozinho.</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Lista de Bancas -->
    <div class="lg:col-span-3">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-slate-500 mb-1 px-1 uppercase tracking-wider">Composição da Banca (<?= count($designacoes) ?>)</h3>
        </div>
        
        <?php if (empty($designacoes)): ?>
            <div class="card p-12 text-center border-dashed border-2">
                <i class="fa-solid fa-users-slash text-3xl text-slate-200 mb-3 block"></i>
                <p class="text-slate-400 text-sm">Nenhum jurado designado para esta prova ainda.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($designacoes as $desig): ?>
                    <div class="card p-4 hover:border-primary-200 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold border border-slate-200">
                                <?= strtoupper(substr($desig->jurado->nome, 0, 1)) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[9px] font-bold text-primary-600 uppercase tracking-widest block mb-0.5">
                                    <?= $criterios[$desig->criterio] ?? $desig->criterio ?>
                                </span>
                                <h4 class="text-sm font-bold text-slate-800 truncate">
                                    <?= e($desig->jurado->nome) ?>
                                </h4>
                                <p class="text-[10px] text-slate-400 truncate"><?= e($desig->jurado->email) ?></p>
                            </div>
                            <form action="<?= route('admin.provas.designacoes.delete', ['id' => $desig->id]) ?>" method="POST" onsubmit="return confirm('Remover este jurado da banca?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-8 h-8 rounded-lg border border-slate-100 text-slate-300 hover:text-red-500 hover:bg-red-50 hover:border-red-100 transition-all">
                                    <i class="fa-solid fa-user-minus text-[10px]"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Resumo FIG -->
            <div class="mt-6 card bg-slate-900 border-none p-6 text-white overflow-hidden relative">
                <div class="relative z-10">
                    <h5 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Resumo da Configuração</h5>
                    <div class="flex gap-8">
                        <div>
                            <p class="text-2xl font-bold font-outfit"><?= $prova->num_jurados ?></p>
                            <p class="text-[10px] text-slate-500 uppercase font-bold">Juízes Requeridos</p>
                        </div>
                        <div class="w-px h-10 bg-slate-800"></div>
                        <div>
                            <p class="text-2xl font-bold font-outfit <?= count($designacoes) >= $prova->num_jurados ? 'text-green-400' : 'text-amber-400' ?>">
                                <?= count($designacoes) ?>
                            </p>
                            <p class="text-[10px] text-slate-500 uppercase font-bold">Designados</p>
                        </div>
                    </div>
                </div>
                <i class="fa-solid fa-gavel absolute -right-6 -bottom-6 text-8xl text-white/5 -rotate-12"></i>
            </div>
        <?php endif; ?>
    </div>
</div>
