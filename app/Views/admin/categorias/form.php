<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex items-center gap-3 mb-6">
    <a href="<?= route('admin.categorias.index') ?>" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-white hover:text-slate-600 transition-all">
        <i class="fa-solid fa-arrow-left text-xs"></i>
    </a>
    <div>
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight"><?= isset($categoria) ? 'Editar' : 'Nova' ?> Categoria</h2>
        <p class="text-xs text-slate-500 font-medium tracking-tight uppercase">Definição Técnica do Nível</p>
    </div>
</div>

<div class="max-w-2xl">
    <div class="card p-8">
        <form action="<?= isset($categoria) ? route('admin.categorias.update', ['id' => $categoria->id]) : route('admin.categorias.store') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nome da Categoria</label>
                    <input name="nome" type="text" value="<?= e(old('nome', $categoria->nome ?? '')) ?>" required class="form-input" placeholder="Ex: Juvenil L1">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Idade Mínima</label>
                        <input name="idade_min" type="number" value="<?= e(old('idade_min', $categoria->idade_min ?? '')) ?>" class="form-input">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Idade Máxima</label>
                        <input name="idade_max" type="number" value="<?= e(old('idade_max', $categoria->idade_max ?? '')) ?>" class="form-input">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Observações/Descrição</label>
                    <textarea name="descricao" rows="4" class="form-input py-3 h-28"><?= e(old('descricao', $categoria->descricao ?? '')) ?></textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="<?= route('admin.categorias.index') ?>" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">Cancelar</a>
                <button type="submit" class="btn btn-primary px-8"> <?= isset($categoria) ? 'Salvar Categoria' : 'Criar Categoria' ?> </button>
            </div>
        </form>
    </div>
</div>
