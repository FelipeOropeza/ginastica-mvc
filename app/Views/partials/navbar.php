<nav class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-lg border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="flex-shrink-0 flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary-500/30">
                        <i class="fa-solid fa-medal text-xl"></i>
                    </div>
                    <span class="text-2xl font-outfit font-extrabold tracking-tight text-slate-900">
                        Gym<span class="text-primary-600">Podium</span>
                    </span>
                </a>
                
                <div class="hidden md:ml-10 md:flex md:space-x-8">
                    <a href="/admin/competicoes" class="inline-flex items-center px-1 pt-1 border-b-2 border-primary-500 text-sm font-semibold text-slate-900 transition-colors">
                        Competições
                    </a>
                    <a href="/admin/atletas" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-slate-500 hover:text-slate-700 hover:border-slate-300 transition-colors">
                        Atletas
                    </a>
                    <a href="/ranking" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-slate-500 hover:text-slate-700 hover:border-slate-300 transition-colors">
                        Ranking Ao Vivo
                    </a>
                </div>
            </div>
            
            <div class="hidden md:flex items-center space-x-4">
                <?php if (isset($user)): ?>
                    <div class="flex items-center gap-3 px-4 py-2 bg-slate-100 rounded-2xl">
                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs">
                            <?= strtoupper(substr($user['nome'], 0, 1)) ?>
                        </div>
                        <span class="text-sm font-semibold text-slate-700"><?= $user['nome'] ?></span>
                        <a href="/logout" class="text-slate-400 hover:text-red-500 transition-colors">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="/login" class="text-sm font-semibold text-slate-700 hover:text-primary-600 transition-colors">Login</a>
                    <a href="/registro" class="btn btn-primary !py-2">Cadastrar Atleta</a>
                <?php endif; ?>
            </div>
            
            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button type="button" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</nav>
