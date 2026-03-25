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

<div class="max-w-2xl" x-data="{ 
    role: '<?= $usuario->role->nome ?? '' ?>',
    roles: { 
        <?php foreach($roles as $r): ?> '<?= $r->id ?>': '<?= $r->nome ?>', <?php endforeach; ?>
    },
    get currentRole() { return this.roles[this.$refs.roleSelect.value] || '' }
}">
    <div class="card p-8">
        <form action="<?= isset($usuario) ? route('admin.usuarios.update', ['id' => $usuario->id]) : route('admin.usuarios.store') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <!-- Seção de Acesso -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-shield-halved text-primary-500 text-xs"></i>
                    <h3 class="text-[11px] font-black uppercase tracking-widest text-slate-800">Dados de Acesso</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest" for="nome">Nome de Usuário</label>
                        <input id="nome" name="nome" type="text" value="<?= e(old('nome', $usuario->nome ?? '')) ?>" required class="form-input">
                    </div>

                    <!-- E-mail -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest" for="email">E-mail</label>
                        <input id="email" name="email" type="email" value="<?= e(old('email', $usuario->email ?? '')) ?>" required class="form-input">
                    </div>

                    <!-- Papel (Role) -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Cargo / Papel</label>
                        <select name="role_id" x-ref="roleSelect" x-init="role = roles[$el.value]" @change="role = roles[$el.value]" class="form-input font-bold text-slate-700">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role->id ?>" <?= ($usuario->role_id ?? 0) == $role->id ? 'selected' : '' ?>>
                                    <?= e(ucfirst($role->nome)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Status da Conta</label>
                        <div class="flex items-center gap-4 py-2">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="ativo" value="1" <?= ($usuario->ativo ?? 1) ? 'checked' : '' ?> class="w-4 h-4 text-primary-600">
                                <span class="text-xs font-bold text-slate-600">Ativado</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="ativo" value="0" <?= !($usuario->ativo ?? 1) ? 'checked' : '' ?> class="w-4 h-4 text-primary-600">
                                <span class="text-xs font-bold text-slate-600">Desativado</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dados Técnicos Dinâmicos (Alpine.js) -->
            <div class="space-y-4 pt-4 animate-in fade-in slide-in-from-top-2 duration-300" x-show="currentRole === 'atleta' || currentRole === 'treinador'">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-id-card text-emerald-500 text-xs"></i>
                    <h3 class="text-[11px] font-black uppercase tracking-widest text-slate-800">Dados Técnicos (<span x-text="currentRole" class="capitalize text-primary-500"></span>)</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome Técnico (Atleta ou Treinador) -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Nome Completo (Súmula)</label>
                        <input name="technical[nome_completo]" type="text" value="<?= e($usuario->atleta->nome_completo ?? $usuario->treinador->nome_completo ?? '') ?>" class="form-input">
                    </div>

                    <!-- Equipe -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Equipe / Clube</label>
                        <select name="technical[equipe_id]" class="form-input">
                            <option value="">Selecione uma equipe</option>
                            <?php foreach ($equipes as $equipe): ?>
                                <?php $currentEqId = ($usuario->atleta->equipe_id ?? $usuario->treinador->equipe_id ?? 0); ?>
                                <option value="<?= $equipe->id ?>" <?= $currentEqId == $equipe->id ? 'selected' : '' ?>><?= e($equipe->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Campos Específicos de Atleta -->
                    <template x-if="currentRole === 'atleta'">
                        <div class="contents">
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Data de Nascimento</label>
                                <input name="technical[data_nascimento]" type="date" value="<?= e($usuario->atleta->data_nascimento ?? '') ?>" class="form-input">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">CPF</label>
                                <input name="technical[cpf]" type="text" value="<?= e($usuario->atleta->cpf ?? '') ?>" placeholder="000.000.000-00" class="form-input">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Categoria</label>
                                <select name="technical[categoria_id]" class="form-input">
                                    <option value="">Selecione</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat->id ?>" <?= ($usuario->atleta->categoria_id ?? 0) == $cat->id ? 'selected' : '' ?>><?= e($cat->nome) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Nº Registro (Federação)</label>
                                <input name="technical[numero_registro]" type="text" value="<?= e($usuario->atleta->numero_registro ?? '') ?>" placeholder="Registro na federação" class="form-input">
                            </div>
                        </div>
                    </template>

                    <!-- Campos Específicos de Treinador -->
                    <template x-if="currentRole === 'treinador'">
                        <div class="contents">
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">CREF</label>
                                <input name="technical[cref]" type="text" value="<?= e($usuario->treinador->cref ?? '') ?>" class="form-input">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Especialidade</label>
                                <input name="technical[especialidade]" type="text" value="<?= e($usuario->treinador->especialidade ?? '') ?>" class="form-input">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Senha -->
            <?php if (!isset($usuario)): ?>
                <div class="space-y-1.5 pt-4">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Senha Provisória</label>
                    <input name="senha" type="password" placeholder="Mínimo 8 caracteres" class="form-input">
                    <p class="text-[9px] text-slate-400 italic mt-1">Padrão: gym123456 (Sugira ao usuário trocar no primeiro acesso)</p>
                </div>
            <?php endif; ?>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="<?= route('admin.usuarios.index') ?>" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-widest">Cancelar</a>
                <button type="submit" class="btn btn-primary px-8 uppercase tracking-widest font-black text-[11px]"> 
                    <?= isset($usuario) ? 'Salvar Alterações' : 'Criar Usuário' ?> 
                </button>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-100 flex gap-3">
        <i class="fa-solid fa-circle-info text-amber-500 mt-0.5"></i>
        <div class="text-[11px] text-amber-800 leading-relaxed font-medium">
            <p><strong>Atenção:</strong> Alterar o papel de um usuário impactará imediatamente as permissões dele. Dados técnicos de atletas ou treinadores serão vinculados automaticamente ao salvar.</p>
        </div>
    </div>
</div>
