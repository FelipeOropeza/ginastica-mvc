<!DOCTYPE html>
<html lang="pt-br" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesse sua conta - GymPodium</title>
    
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
</head>
<body class="bg-slate-50 font-sans h-full flex items-center justify-center p-4">

    <div class="w-full max-w-sm z-10">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-primary-600 rounded-xl text-white text-xl mb-4 shadow-sm">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <h1 class="text-3xl font-outfit font-bold text-slate-900 tracking-tight">Gym<span class="text-primary-600">Admin</span></h1>
            <p class="text-slate-500 text-sm mt-1">Plataforma de Gestão Esportiva</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Acesse sua conta</h2>

            <?php if ($error = errors('email')): ?>
                <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-2.5 rounded-lg mb-6 text-xs flex items-center gap-2 font-medium">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST" class="space-y-5">
                <?= csrf_field() ?>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="email">E-mail</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        value="<?= e(old('email')) ?>" 
                        required 
                        autofocus
                        placeholder="admin@gympodium.com"
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-600 transition-all text-sm text-slate-900 placeholder:text-slate-400"
                    >
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="senha">Senha</label>
                        <a href="#" class="text-[10px] font-bold text-primary-600 hover:text-primary-700 transition-colors">Esqueceu?</a>
                    </div>
                    <input 
                        id="senha" 
                        name="senha" 
                        type="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-600 transition-all text-sm text-slate-900 placeholder:text-slate-400"
                    >
                </div>

                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500/30 cursor-pointer">
                        <span class="text-xs font-medium text-slate-500 group-hover:text-slate-700 transition-colors">Manter conectado</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 bg-primary-600 text-white rounded-lg font-bold shadow-sm hover:bg-primary-700 active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-sm">
                    <span>Entrar no Painel</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-slate-500 text-xs">Ainda não tem acesso?</p>
                <a href="/register" class="inline-block mt-2 text-xs font-bold text-primary-600 hover:text-primary-700 hover:underline">
                    Solicitar Registro de Staff
                </a>
            </div>
        </div>

        <!-- Footer Info -->
        <p class="text-center text-slate-400 text-[10px] mt-8 uppercase tracking-widest font-bold">
            &copy; <?= date('Y') ?> GymPodium &bull; v2.5
        </p>
    </div>

</body>
</html>
