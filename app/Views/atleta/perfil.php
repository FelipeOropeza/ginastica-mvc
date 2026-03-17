<?php $this->layout('layouts/athlete', ['title' => $title]) ?>

<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Meus Dados de Atleta</h2>
        <p class="text-sm text-slate-500">Mantenha suas informações atualizadas para competições.</p>
    </div>

    <form action="<?= route('atleta.profile.update') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>

        <div class="card p-8 space-y-6">
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nome Completo</label>
                <input name="nome_completo" type="text" value="<?= e(old('nome_completo', $atleta->nome_completo ?? session()->get('user.nome'))) ?>" required class="form-input">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Data de Nascimento</label>
                    <input name="data_nascimento" type="date" value="<?= e(old('data_nascimento', $atleta->data_nascimento ?? '')) ?>" required class="form-input">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">CPF (Opcional)</label>
                    <input name="cpf" type="text" value="<?= e(old('cpf', $atleta->cpf ?? '')) ?>" class="form-input" placeholder="000.000.000-00">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Minha Equipe / Clube</label>
                    <select name="equipe_id" required class="form-input">
                        <option value="">Selecione seu clube...</option>
                        <?php foreach ($equipes as $equipe): ?>
                            <option value="<?= $equipe->id ?>" <?= ($atleta->equipe_id ?? 0) == $equipe->id ? 'selected' : '' ?>>
                                <?= e($equipe->nome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Categoria / Nível</label>
                    <select name="categoria_id" required class="form-input">
                        <option value="">Selecione sua categoria...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= ($atleta->categoria_id ?? 0) == $cat->id ? 'selected' : '' ?>>
                                <?= e($cat->nome) ?> (<?= $cat->idade_min ?> a <?= $cat->idade_max ?> anos)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="btn btn-primary px-10 py-3 shadow-lg shadow-primary-500/20">
                    Salvar Dados do Perfil
                </button>
            </div>
        </div>
    </form>
    
    <div class="mt-8 flex items-center justify-center gap-2 text-slate-400">
        <i class="fa-solid fa-lock text-xs"></i>
        <p class="text-[10px] font-bold uppercase tracking-widest">Acesso Seguro</p>
    </div>
</div>
