<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="/admin/competicoes" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 transition-all">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-3xl font-outfit font-extrabold text-slate-900"><?= isset($competicao) ? 'Editar' : 'Nova' ?> Competição</h2>
            <p class="text-slate-500">Preencha os dados básicos do evento.</p>
        </div>
    </div>

    <form action="<?= isset($competicao) ? route('admin.competicoes.update', ['id' => $competicao->id]) : route('admin.competicoes.store') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>
        
        <?php if (isset($competicao)): ?>
            <input type="hidden" name="id" value="<?= $competicao->id ?>">
        <?php endif; ?>
        
        <div class="card p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nome da Competição</label>
                    <input type="text" name="nome" value="<?= e(old('nome', $competicao->nome ?? '')) ?>" required
                        class="w-full px-4 py-3 rounded-xl border <?= errors('nome') ? 'border-red-500' : 'border-slate-200' ?> focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none"
                        placeholder="Ex: I Torneio Regional de Barueri">
                    <?php if (errors('nome')): ?>
                        <p class="text-red-500 text-xs mt-1 font-bold"><?= e(errors('nome')) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Data de Início</label>
                    <div class="relative">
                        <i class="fa-solid fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="data_inicio" value="<?= e(old('data_inicio', $competicao->data_inicio ?? '')) ?>" required
                            class="w-full pl-12 pr-4 py-3 rounded-xl border <?= errors('data_inicio') ? 'border-red-500' : 'border-slate-200' ?> focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                    <?php if (errors('data_inicio')): ?>
                        <p class="text-red-500 text-xs mt-1 font-bold"><?= e(errors('data_inicio')) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Data de Término</label>
                    <div class="relative">
                        <i class="fa-solid fa-calendar-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="data_fim" value="<?= e(old('data_fim', $competicao->data_fim ?? '')) ?>" required
                            class="w-full pl-12 pr-4 py-3 rounded-xl border <?= errors('data_fim') ? 'border-red-500' : 'border-slate-200' ?> focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none">
                    </div>
                    <?php if (errors('data_fim')): ?>
                        <p class="text-red-500 text-xs mt-1 font-bold"><?= e(errors('data_fim')) ?></p>
                    <?php endif; ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Local / Ginásio</label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="local" value="<?= e(old('local', $competicao->local ?? '')) ?>" required
                            class="w-full pl-12 pr-4 py-3 rounded-xl border <?= errors('local') ? 'border-red-500' : 'border-slate-200' ?> focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none"
                            placeholder="Ex: Ginásio Poliesportivo José Corrêa">
                    </div>
                    <?php if (errors('local')): ?>
                        <p class="text-red-500 text-xs mt-1 font-bold"><?= e(errors('local')) ?></p>
                    <?php endif; ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Descrição / Informações Adicionais</label>
                    <textarea name="descricao" rows="4"
                        class="w-full px-4 py-3 rounded-xl border <?= errors('descricao') ? 'border-red-500' : 'border-slate-200' ?> focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none"
                        placeholder="Detalhes sobre a competição, premiação, categorias..."><?= e(old('descricao', $competicao->descricao ?? '')) ?></textarea>
                    <?php if (errors('descricao')): ?>
                        <p class="text-red-500 text-xs mt-1 font-bold"><?= e(errors('descricao')) ?></p>
                    <?php endif; ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Status da Competição</label>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <?php 
                            $statusOptions = [
                                'rascunho' => ['label' => 'Rascunho', 'color' => 'slate', 'icon' => 'fa-file-lines'],
                                'aberta'   => ['label' => 'Aberta (Inscrições)', 'color' => 'green', 'icon' => 'fa-door-open'],
                                'em_andamento' => ['label' => 'Em Andamento', 'color' => 'blue', 'icon' => 'fa-person-running'],
                                'encerrada' => ['label' => 'Encerrada', 'color' => 'red', 'icon' => 'fa-calendar-check'],
                            ];
                            $currentStatus = old('status', $competicao->status ?? 'rascunho');
                        ?>
                        <?php foreach($statusOptions as $val => $opt): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="<?= $val ?>" class="peer hidden" <?= $currentStatus === $val ? 'checked' : '' ?>>
                                <div class="peer-checked:border-primary-600 peer-checked:bg-primary-50 border-2 border-slate-100 rounded-xl p-4 flex flex-col items-center gap-2 hover:border-slate-200 transition-all">
                                    <i class="fa-solid <?= $opt['icon'] ?> text-lg text-<?= $opt['color'] ?>-500"></i>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-600"><?= $opt['label'] ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="<?= route('admin.competicoes.index') ?>" class="btn px-6 py-3 rounded-xl font-bold bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">Cancelar</a>
            <button type="submit" class="btn btn-primary min-w-[150px]">
                <i class="fa-solid fa-save mr-2"></i> Salvar Competição
            </button>
        </div>
    </form>
</div>
