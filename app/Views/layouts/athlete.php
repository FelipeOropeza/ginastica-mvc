<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Atleta' ?> - GymPodium</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style type="text/tailwindcss">
        @layer components {
            .nav-link {
                @apply flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 text-slate-400 hover:text-white hover:bg-slate-800 font-medium text-sm;
            }
            .nav-link.active {
                @apply text-white bg-primary-600 shadow-sm;
            }
            .card {
                @apply bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden;
            }
            .btn {
                @apply inline-flex items-center justify-center px-4 py-2 rounded-lg font-semibold transition-all duration-200 active:scale-95 text-sm;
            }
            .btn-primary {
                @apply bg-primary-600 text-white hover:bg-primary-700 shadow-sm border border-primary-500;
            }
            .form-input {
                @apply w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all outline-none text-sm;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 hidden md:flex flex-col h-screen sticky top-0 border-r border-slate-800 shrink-0">
        <div class="p-6">
            <a href="/atleta/dashboard" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white shadow-sm">
                    <i class="fa-solid fa-person-running text-sm"></i>
                </div>
                <span class="text-lg font-outfit font-bold tracking-tight text-white">
                    Gym<span class="text-primary-500">Podium</span>
                </span>
            </a>
        </div>
        
        <nav class="flex-1 px-3 space-y-1 mt-2">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider px-3 mb-2 opacity-50">Atleta</div>
            <a href="/atleta/dashboard" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-house w-4"></i> Início
            </a>
            <a href="/atleta/perfil" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'perfil') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-gear w-4"></i> Meu Perfil
            </a>
            <a href="/atleta/competicoes" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'competicoes') ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-star w-4"></i> Minhas Competições
            </a>
        </nav>
        
        <div class="p-4 border-t border-slate-800">
            <?php $sessionUser = session()->get('user'); ?>
            <div class="bg-slate-800/50 rounded-lg p-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-primary-600 text-white flex items-center justify-center font-bold text-xs uppercase">
                    <?= strtoupper(substr($sessionUser['nome'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate"><?= $sessionUser['nome'] ?? 'Atleta' ?></p>
                    <p class="text-[10px] text-slate-400 truncate uppercase tracking-tighter">Atleta</p>
                </div>
                <a href="/logout" class="text-slate-500 hover:text-red-400 cursor-pointer p-1">
                    <i class="fa-solid fa-power-off text-xs"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Header -->
        <header class="bg-white border-b border-slate-200 h-20 sticky top-0 z-30 flex items-center px-8">
            <h1 class="text-xl font-outfit font-bold text-slate-900"><?= $title ?? 'Painel' ?></h1>
        </header>
        
        <main class="p-8 pb-12">
            <?php $this->renderSection('content') ?>
        </main>
    </div>
</body>
</html>
