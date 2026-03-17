<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-outfit font-bold text-slate-800 tracking-tight">Gestão de Usuários</h2>
        <p class="text-sm text-slate-500">Administre os acessos de administradores, jurados e atletas.</p>
    </div>
    <a href="<?= route('admin.usuarios.create') ?>" class="btn btn-primary gap-2 w-fit">
        <i class="fa-solid fa-user-plus text-xs"></i> Novo Usuário
    </a>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Usuário</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Papel / Cargo</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Cadastro</th>
                    <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($usuarios as $user): ?>
                    <tr id="user-<?= $user->id ?>" class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-xs font-bold border border-slate-200">
                                    <?= strtoupper(substr($user->nome, 0, 1)) ?>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800 text-sm truncate"><?= e($user->nome) ?></p>
                                    <p class="text-[10px] text-slate-400 truncate"><?= e($user->email) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <?php 
                                $roleClasses = [
                                    'admin' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                    'jurado' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'atleta' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'operador' => 'bg-slate-50 text-slate-700 border-slate-100',
                                ];
                                $roleClass = $roleClasses[$user->role->nome ?? ''] ?? 'bg-slate-50 text-slate-700 border-slate-100';
                            ?>
                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold border <?= $roleClass ?>">
                                <?= e($user->role->nome ?? 'Usuário') ?>
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <button 
                                hx-post="<?= route('admin.usuarios.toggle', ['id' => $user->id]) ?>"
                                hx-headers='{"X-CSRF-TOKEN": "<?= csrf_token() ?>"}'
                                class="flex items-center gap-1.5 group cursor-pointer"
                            >
                                <div class="w-2.5 h-2.5 rounded-full <?= $user->ativo ? 'bg-green-500' : 'bg-slate-300' ?> transition-colors shadow-sm"></div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase group-hover:text-slate-800 transition-colors">
                                    <?= $user->ativo ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </button>
                        </td>
                        <td class="px-5 py-4 text-[10px] text-slate-400 font-medium">
                            <?= date('d/m/Y', strtotime($user->created_at)) ?>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex justify-end gap-1.5">
                                <a href="<?= route('admin.usuarios.edit', ['id' => $user->id]) ?>" title="Editar Usuário" class="w-7 h-7 rounded border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-white hover:text-amber-600 transition-all shadow-sm">
                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                </a>
                                <?php if (session()->get('user.id') != $user->id): ?>
                                    <button 
                                        hx-post="<?= route('admin.usuarios.delete', ['id' => $user->id]) ?>"
                                        hx-headers='{"X-CSRF-TOKEN": "<?= csrf_token() ?>"}'
                                        hx-confirm="Excluir usuário permanentemente?"
                                        hx-target="#user-<?= $user->id ?>"
                                        hx-swap="outerHTML"
                                        class="w-7 h-7 rounded border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all shadow-sm"
                                    >
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
