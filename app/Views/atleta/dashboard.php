<?php $this->layout('layouts/athlete', ['title' => $title]) ?>

<?php if ($perfilIncompleto): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 mb-8 flex flex-col md:flex-row items-center gap-6 animate-pulse">
        <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
            <i class="fa-solid fa-address-card text-2xl"></i>
        </div>
        <div class="flex-1 text-center md:text-left">
            <h3 class="text-amber-900 font-bold text-lg mb-1">Perfil Incompleto!</h3>
            <p class="text-amber-700 text-sm leading-relaxed">
                Para se inscrever em competições, precisamos que você complete seu cadastro com sua data de nascimento, equipe e categoria.
            </p>
        </div>
        <a href="<?= route('atleta.profile') ?>" class="btn bg-amber-600 text-white hover:bg-amber-700 shadow-lg shadow-amber-600/20 px-6 py-3 shrink-0">
            Completar Agora
        </a>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="card p-6 bg-gradient-to-br from-primary-600 to-primary-800 text-white border-none relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-xs font-bold uppercase tracking-widest opacity-80 mb-1">Bem-vindo(a),</h3>
            <p class="text-2xl font-outfit font-bold mb-4"><?= e(session()->get('user.nome')) ?></p>
            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3 inline-block">
                <p class="text-[10px] font-bold uppercase tracking-tighter">Status: 
                    <span class="<?= $atleta->ativo ?? false ? 'text-green-300' : 'text-amber-300' ?>">
                        <?= $atleta->ativo ?? false ? 'Regularizado' : 'Pendente' ?>
                    </span>
                </p>
            </div>
        </div>
        <i class="fa-solid fa-star absolute -right-4 -bottom-4 text-7xl text-white/10 -rotate-12"></i>
    </div>

    <div class="card p-6 flex flex-col justify-center">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 text-center">Inscrições Ativas</p>
        <p class="text-4xl font-outfit font-black text-slate-800 text-center">0</p>
    </div>

    <div class="card p-6 flex flex-col justify-center">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 text-center">Melhor Nota</p>
        <p class="text-4xl font-outfit font-black text-slate-800 text-center">--</p>
    </div>
</div>

<div class="mt-8">
    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Minhas Atividades Recentes</h3>
    <div class="card p-12 text-center border-dashed border-2">
        <i class="fa-solid fa-ghost text-3xl text-slate-200 mb-2"></i>
        <p class="text-slate-400 text-sm italic">Nenhum evento ou nota registrada no seu histórico.</p>
    </div>
</div>
