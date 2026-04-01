<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Painel Admin' ?> - GymPodium</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://unpkg.com/htmx.org@2.0.2"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    
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
                        sidebar: '#0f172a'
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
                @apply bg-white rounded-xl border border-slate-200 shadow-sm;
            }
            .btn {
                @apply inline-flex items-center justify-center px-4 py-2 rounded-lg font-semibold transition-all duration-200 active:scale-95 text-sm;
            }
            .btn-primary {
                @apply bg-primary-600 text-white hover:bg-primary-700 shadow-sm;
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
            <a href="/admin/dashboard" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white shadow-sm">
                    <i class="fa-solid fa-trophy text-sm"></i>
                </div>
                <span class="text-lg font-outfit font-bold tracking-tight text-white">
                    Gym<span class="text-primary-500">Admin</span>
                </span>
            </a>
        </div>
        
        <nav class="flex-1 px-3 space-y-1 mt-2">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider px-3 mb-2 opacity-50">Menu</div>
            <a href="<?= route('admin.dashboard') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-pie w-4"></i> Dashboard
            </a>
            <a href="<?= route('admin.competicoes.index') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'competicoes') ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-check w-4"></i> Competições
            </a>
            <a href="<?= route('admin.usuarios.index') ?>" class="nav-link <?= (str_contains($_SERVER['REQUEST_URI'], 'usuarios') || str_contains($_SERVER['REQUEST_URI'], 'atletas')) ? 'active' : '' ?>">
                <i class="fa-solid fa-users w-4"></i> Usurios
            </a>
            <a href="<?= route('admin.assistente.index') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'assistente') ? 'active' : '' ?>">
                <i class="fa-solid fa-robot w-4 text-indigo-400"></i> Assistente IA
            </a>
            
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider px-3 mt-6 mb-2 opacity-50">Configurações</div>
            <a href="<?= route('admin.equipes.index') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'equipes') ? 'active' : '' ?>">
                <i class="fa-solid fa-users-viewfinder w-4"></i> Equipes
            </a>
            <a href="<?= route('admin.categorias.index') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'categorias') ? 'active' : '' ?>">
                <i class="fa-solid fa-layer-group w-4"></i> Categorias
            </a>
        </nav>
        
        <div class="p-4 border-t border-slate-800">
            <?php $sessionUser = session()->get('user'); ?>
            <div class="bg-slate-800/50 rounded-lg p-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-primary-600 text-white flex items-center justify-center font-bold text-xs">
                    <?= strtoupper(substr($sessionUser['nome'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate"><?= $sessionUser['nome'] ?? 'Admin' ?></p>
                    <p class="text-[10px] text-slate-400 truncate uppercase tracking-tighter">Administrador</p>
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
            <div class="ml-auto flex items-center gap-4">
                <button class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200">
                    <i class="fa-solid fa-bell"></i>
                </button>
            </div>
        </header>
        
        <main class="p-8 pb-12">
            <?php $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Alert System -->
    <div id="alerts" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

    <script>
        // Função global para mostrar alertas
        function showToast(message, type = 'error') {
            if (!message) return;
            const alerts = document.getElementById('alerts');
            if (!alerts) return;
            const alert = document.createElement('div');
            
            const bgColor = type === 'error' ? 'bg-rose-600' : 'bg-emerald-600';
            const icon = type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check';
            
            alert.className = `${bgColor} text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 animate-all transform transition-all duration-300 translate-x-12 opacity-0`;
            alert.innerHTML = `
                <i class="fa-solid ${icon} text-lg"></i>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-70">${type === 'error' ? 'Erro de Validação' : 'Sucesso'}</p>
                    <p class="text-sm font-bold leading-tight">${message}</p>
                </div>
            `;
            
            alerts.appendChild(alert);
            
            // Entrada
            setTimeout(() => {
                alert.classList.remove('translate-x-12', 'opacity-0');
            }, 10);
            
            // Saída
            setTimeout(() => {
                alert.classList.add('translate-x-12', 'opacity-20');
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }

        // Listener para triggers do HTMX
        document.body.addEventListener('showAlert', function(evt) {
            showToast(evt.detail.message, evt.detail.type);
        });

        // Fallback para erros genéricos do HTMX (500, etc)
        document.body.addEventListener('htmx:afterRequest', function(evt) {
            if (evt.detail.xhr.status >= 400 && evt.detail.xhr.status !== 422) {
                showToast("Ocorreu um erro inesperado no servidor.", "error");
            }
        });

        // Alertas de Sessão (Mensagens Flash)
        document.addEventListener('DOMContentLoaded', () => {
            <?php if ($msg = session()->get('success')): ?>
                showToast("<?= e($msg) ?>", "success");
            <?php endif; ?>
            <?php if ($msg = session()->get('error')): ?>
                showToast("<?= e($msg) ?>", "error");
            <?php endif; ?>
        });
    </script>
</body>
</html>
