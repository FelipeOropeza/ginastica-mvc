<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex items-center gap-3 mb-6">
    <a href="<?= route('admin.equipes.index') ?>" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-white hover:text-slate-600 transition-all">
        <i class="fa-solid fa-arrow-left text-xs"></i>
    </a>
    <div>
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight"><?= isset($equipe) ? 'Editar' : 'Nova' ?> Equipe</h2>
        <p class="text-xs text-slate-500 font-medium tracking-tight uppercase"><?= isset($equipe) ? e($equipe->nome) : 'Preencha os dados da agremiação' ?></p>
    </div>
</div>

<div class="max-w-2xl">
    <div class="card p-8">
        <form action="<?= isset($equipe) ? route('admin.equipes.update', ['id' => $equipe->id]) : route('admin.equipes.store') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nome da Equipe</label>
                    <input name="nome" type="text" value="<?= e(old('nome', $equipe->nome ?? '')) ?>" required class="form-input" placeholder="Ex: Clube de Ginástica X">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Cidade</label>
                        <input name="cidade" type="text" value="<?= e(old('cidade', $equipe->cidade ?? '')) ?>" class="form-input">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Estado (UF)</label>
                        <input name="estado" type="text" value="<?= e(old('estado', $equipe->estado ?? '')) ?>" class="form-input" maxlength="2">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Cores da Equipe</label>
                    <input name="cores" type="text" value="<?= e(old('cores', $equipe->cores ?? '')) ?>" class="form-input" placeholder="Ex: Verde e Amarelo">
                </div>

                <div class="pt-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="ativo" value="1" <?= ($equipe->ativo ?? 1) ? 'checked' : '' ?> class="text-primary-600">
                            <span class="text-xs font-bold text-slate-600">Ativa</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="ativo" value="0" <?= !($equipe->ativo ?? 1) ? 'checked' : '' ?> class="text-primary-600">
                            <span class="text-xs font-bold text-slate-600">Inativa</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="<?= route('admin.equipes.index') ?>" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">Cancelar</a>
                <button type="submit" class="btn btn-primary px-8"> <?= isset($equipe) ? 'Salvar Equipe' : 'Criar Equipe' ?> </button>
            </div>
        </form>
    </div>
</div>
