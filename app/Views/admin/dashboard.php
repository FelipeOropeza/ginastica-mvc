<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <!-- Card Stats -->
    <div class="card p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Competições Ativas</p>
                <h3 class="text-2xl font-outfit font-bold text-slate-900 mt-1"><?= $stats['competicoes_ativas'] ?></h3>
            </div>
            <div class="w-10 h-10 bg-primary-50 text-primary-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fa-solid fa-trophy"></i>
            </div>
        </div>
        <p class="text-[10px] text-green-600 font-bold mt-4 flex items-center gap-1">
            <i class="fa-solid fa-arrow-up"></i> +2 este mês
        </p>
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Atletas</p>
                <h3 class="text-2xl font-outfit font-bold text-slate-900 mt-1"><?= $stats['total_atletas'] ?></h3>
            </div>
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fa-solid fa-user-group"></i>
            </div>
        </div>
        <p class="text-[10px] text-slate-400 mt-4 italic">Base de dados completa</p>
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Próximos Eventos</p>
                <h3 class="text-2xl font-outfit font-bold text-slate-900 mt-1"><?= count($stats['proximas_competicoes']) ?></h3>
            </div>
            <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
        </div>
        <p class="text-[10px] text-slate-400 mt-4 italic">Aguardando início</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Próximas Competições -->
    <div class="card p-5">
        <div class="flex justify-between items-center mb-6 px-1">
            <h4 class="text-lg font-bold text-slate-900">Novas Competições</h4>
            <a href="/admin/competicoes" class="text-xs font-bold text-primary-600 hover:underline">Ver todas</a>
        </div>
        
        <div class="space-y-3">
            <?php foreach ($stats['proximas_competicoes'] as $comp): ?>
                <div class="flex items-center gap-4 p-3 rounded-lg border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition-all group">
                    <div class="w-10 h-10 rounded bg-slate-100 flex flex-col items-center justify-center text-slate-500 shrink-0">
                        <span class="text-[9px] uppercase font-bold"><?= date('M', strtotime($comp->data_inicio)) ?></span>
                        <span class="text-sm font-bold leading-none"><?= date('d', strtotime($comp->data_inicio)) ?></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate"><?= $comp->nome ?></p>
                        <p class="text-[11px] text-slate-500 truncate"><?= $comp->local ?></p>
                    </div>
                    <a href="<?= route('admin.competicoes.edit', ['id' => $comp->id]) ?>" class="text-slate-300 hover:text-slate-600 p-2">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($stats['proximas_competicoes'])): ?>
                <p class="text-center text-slate-400 py-8 text-sm italic">Nenhuma competição pendente.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card p-5">
        <h4 class="text-lg font-bold text-slate-900 mb-6 px-1">Ações Rápidas</h4>
        <div class="grid grid-cols-2 gap-3">
            <a href="/admin/competicoes/criar" class="flex flex-col items-center text-center p-4 rounded-lg bg-slate-50 border border-slate-100 hover:border-primary-200 hover:bg-primary-50 transition-all group">
                <i class="fa-solid fa-plus-circle text-xl mb-2 text-primary-600"></i>
                <p class="font-bold text-xs text-slate-700">Nova Competição</p>
            </a>
            <a href="/admin/usuarios" class="flex flex-col items-center text-center p-4 rounded-lg bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50 transition-all group">
                <i class="fa-solid fa-user-plus text-xl mb-2 text-blue-600"></i>
                <p class="font-bold text-xs text-slate-700">Novo Usuário</p>
            </a>
            <a href="/admin/relatorios" class="flex flex-col items-center text-center p-4 rounded-lg bg-slate-50 border border-slate-100 hover:border-green-200 hover:bg-green-50 transition-all group">
                <i class="fa-solid fa-file-export text-xl mb-2 text-green-600"></i>
                <p class="font-bold text-xs text-slate-700">Relatórios</p>
            </a>
            <a href="/admin/configuracoes" class="flex flex-col items-center text-center p-4 rounded-lg bg-slate-50 border border-slate-100 hover:border-amber-200 hover:bg-amber-50 transition-all group">
                <i class="fa-solid fa-cog text-xl mb-2 text-amber-600"></i>
                <p class="font-bold text-xs text-slate-700">Configurações</p>
            </a>
        </div>
    </div>
</div>
