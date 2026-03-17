<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex items-center gap-3 mb-6">
    <a href="<?= route('admin.usuarios.index') ?>" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-white hover:text-slate-600 transition-all">
        <i class="fa-solid fa-arrow-left text-xs"></i>
    </a>
    <div>
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight"><?= isset($usuario) ? 'Editar' : 'Novo' ?> Usuário</h2>
        <p class="text-xs text-slate-500 font-medium tracking-tight uppercase"><?= isset($usuario) ? e($usuario->nome) : 'Preencha as informações do novo membro' ?></p>
    </div>
</div>

<div class="max-w-2xl">
    <div class="card p-8">
        <form action="<?= isset($usuario) ? route('admin.usuarios.update', ['id' => $usuario->id]) : route('admin.usuarios.store') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="nome">Nome Completo</label>
                    <input 
                        id="nome" 
                        name="nome" 
                        type="text" 
                        value="<?= e(old('nome', $usuario->nome ?? '')) ?>" 
                        required 
                        class="form-input"
                    >
                    <?php if ($error = errors('nome')): ?>
                        <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1"><?= e($error) ?></p>
                    <?php endif; ?>
                </div>

                <!-- E-mail -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="email">E-mail</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        value="<?= e(old('email', $usuario->email ?? '')) ?>" 
                        required 
                        class="form-input"
                    >
                    <?php if ($error = errors('email')): ?>
                        <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1"><?= e($error) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Papel (Role) -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Cargo / Papel</label>
                    <select name="role_id" class="form-input">
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role->id ?>" <?= ($usuario->role_id ?? 0) == $role->id ? 'selected' : '' ?>>
                                <?= e(ucfirst($role->nome)) ?> - <?= e($role->descricao) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Status da Conta</label>
                    <div class="flex items-center gap-4 py-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="ativo" value="1" <?= ($usuario->ativo ?? 1) ? 'checked' : '' ?> class="w-4 h-4 text-primary-600 focus:ring-primary-500/20">
                            <span class="text-xs font-bold text-slate-600">Ativado</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="ativo" value="0" <?= !($usuario->ativo ?? 1) ? 'checked' : '' ?> class="w-4 h-4 text-primary-600 focus:ring-primary-500/20">
                            <span class="text-xs font-bold text-slate-600">Desativado</span>
                        </label>
                    </div>
                </div>

                <!-- Senha (Apenas na Criação) -->
                <?php if (!isset($usuario)): ?>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Senha Provisória</label>
                        <input name="senha" type="password" placeholder="Mínimo 8 caracteres" class="form-input">
                        <p class="text-[9px] text-slate-400 italic">Padrão: gym123456</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="<?= route('admin.usuarios.index') ?>" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">Cancelar</a>
                <button type="submit" class="btn btn-primary px-8"> <?= isset($usuario) ? 'Salvar Alterações' : 'Criar Usuário' ?> </button>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-100 flex gap-3">
        <i class="fa-solid fa-circle-info text-amber-500 mt-0.5"></i>
        <div class="text-[11px] text-amber-800 leading-relaxed font-medium">
            <p><strong>Atenção:</strong> Alterar o papel de um usuário impactará imediatamente as permissões de acesso dele no sistema. Atletas não podem ter acesso ao painel de jurados e vice-versa.</p>
        </div>
    </div>
</div>
