<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex items-center gap-3 mb-6">
    <a href="/admin/competicoes" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-white hover:text-slate-600 transition-all">
        <i class="fa-solid fa-arrow-left text-xs"></i>
    </a>
    <div>
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Configurar Provas</h2>
        <p class="text-xs text-slate-500 font-medium tracking-tight uppercase"><?= $competicao->nome ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Form Nova Prova -->
    <div class="lg:col-span-1">
        <div class="card p-5 h-fit sticky top-24">
            <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2 uppercase tracking-wider">
                <i class="fa-solid fa-circle-plus text-primary-600"></i> Novo Aparelho
            </h3>
            
            <form action="/admin/competicoes/<?= $competicao->id ?>/provas/store" method="POST" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Aparelho</label>
                    <select name="aparelho" class="form-input">
                        <option value="solo">Solo</option>
                        <option value="salto">Salto</option>
                        <option value="barras_assimetricas">Barras Assimétricas</option>
                        <option value="trave">Trave</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Categoria</label>
                    <select name="categoria_id" class="form-input">
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat->id ?>"><?= $cat->nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Cálculo</label>
                    <select name="tipo_calculo" class="form-input">
                        <option value="media_simples">Média Aritmética</option>
                        <option value="media_sem_extremos">Média Olímpica</option>
                        <option value="nota_d_mais_e">D + E (FIG)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Jurados</label>
                    <input type="number" name="num_jurados" value="3" min="1" max="10" class="form-input">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Máx. Participantes</label>
                    <input type="number" name="max_participantes" placeholder="Ex: 20" min="1" class="form-input">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Descrição</label>
                    <textarea name="descricao" rows="2" placeholder="Opcional..." class="form-input"></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-2">
                    Adicionar Prova
                </button>
            </form>
        </div>
    </div>

    <!-- Lista de Provas -->
    <div class="lg:col-span-3">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-500 mb-2 px-1 uppercase tracking-wider">Provas Registradas</h3>
        </div>
        
        <?php if (empty($provas)): ?>
            <div class="card p-12 text-center border-dashed border-2">
                <p class="text-slate-400 text-sm">Nenhum aparelho configurado para esta competição.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <?php foreach ($provas as $prova): ?>
                    <div class="card p-4 hover:border-primary-200 transition-colors">
                        <div class="flex justify-between items-start mb-3">
                            <div class="w-8 h-8 rounded bg-slate-50 text-slate-400 flex items-center justify-center text-sm border border-slate-100">
                                <?php 
                                    $icons = [
                                        'solo' => 'fa-child-reaching',
                                        'salto' => 'fa-person-running',
                                        'barras_assimetricas' => 'fa-bars-staggered',
                                        'trave' => 'fa-grip-lines'
                                    ];
                                    $icon = $icons[$prova->aparelho] ?? 'fa-medal';
                                ?>
                                <i class="fa-solid <?= $icon ?>"></i>
                            </div>
                            <form action="/admin/provas/<?= $prova->id ?>/deletar" method="POST">
                                <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </form>
                        </div>
                        
                        <div>
                            <span class="text-[9px] font-bold text-primary-600 uppercase tracking-widest block">
                                <?= str_replace('_', ' ', strtoupper($prova->aparelho)) ?>
                            </span>
                            <h4 class="text-sm font-bold text-slate-800 mb-2 truncate">
                                <?php 
                                    $cat = array_filter($categorias, fn($c) => $c->id == $prova->categoria_id);
                                    $cat = reset($cat);
                                    echo $cat ? $cat->nome : 'N/A';
                                ?>
                                </h4>
                            
                            <?php if ($prova->descricao): ?>
                                <p class="text-[10px] text-slate-400 italic mb-3 line-clamp-1" title="<?= e($prova->descricao) ?>">
                                    <?= e($prova->descricao) ?>
                                </p>
                            <?php endif; ?>

                            <div class="flex flex-wrap gap-1.5 mt-3">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-[9px] font-bold text-slate-500 uppercase border border-slate-200/50">
                                    <?= str_replace('_', ' ', $prova->tipo_calculo) ?>
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-[9px] font-bold text-slate-500 uppercase border border-slate-200/50">
                                    <?= $prova->num_jurados ?> Juízes
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-amber-50 text-[9px] font-bold text-amber-600 uppercase border border-amber-200/30">
                                    <i class="fa-solid fa-users mr-1"></i> <?= $prova->max_participantes ?: 'Ilimitado' ?>
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-50">
                            <a href="/admin/provas/<?= $prova->id ?>/designacoes" class="text-[11px] font-bold text-primary-600 hover:text-primary-700 flex items-center justify-between group/link">
                                Designar Jurados <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
