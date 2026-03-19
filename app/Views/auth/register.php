<!DOCTYPE html>
<html lang="pt-br" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Acesso - GymPodium</title>

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

    <div class="w-full max-w-lg z-10 my-8">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-primary-600 rounded-xl text-white text-xl mb-4 shadow-sm">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 class="text-3xl font-outfit font-bold text-slate-900 tracking-tight">Gym<span class="text-primary-600">Admin</span></h1>
            <p class="text-slate-500 text-sm mt-1">Plataforma de Gestão Esportiva</p>
        </div>

        <!-- Registration Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
            <h2 class="text-xl font-bold text-slate-900 mb-2">Solicitar Registro</h2>
            <p class="text-slate-400 text-xs mb-6 font-medium">Preencha os dados abaixo para criar sua conta de membro staff.</p>

            <form action="/register" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Nome -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="nome">Nome Completo</label>
                    <input
                        id="nome"
                        name="nome"
                        type="text"
                        value="<?= e(old('nome')) ?>"

                        placeholder="Nome de exibição"
                        class="w-full px-4 py-2 bg-white border <?= errors('nome') ? 'border-red-300 ring-4 ring-red-500/5' : 'border-slate-300' ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-600 transition-all text-sm text-slate-900 placeholder:text-slate-400">
                    <?php if ($error = errors('nome')): ?>
                        <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1"><?= e($error) ?></p>
                    <?php endif; ?>
                </div>

                <!-- E-mail -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="email">E-mail Corporativo</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="<?= e(old('email')) ?>"

                        placeholder="exemplo@academia.com"
                        class="w-full px-4 py-2 bg-white border <?= errors('email') ? 'border-red-300 ring-4 ring-red-500/5' : 'border-slate-300' ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-600 transition-all text-sm text-slate-900 placeholder:text-slate-400">
                    <?php if ($error = errors('email')): ?>
                        <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1"><?= e($error) ?></p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Senha -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="senha">Senha</label>
                        <input
                            id="senha"
                            name="senha"
                            type="password"

                            placeholder="••••••••"
                            class="w-full px-4 py-2 bg-white border <?= errors('senha') ? 'border-red-300 ring-4 ring-red-500/5' : 'border-slate-300' ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-600 transition-all text-sm text-slate-900 placeholder:text-slate-400">
                        <?php if ($error = errors('senha')): ?>
                            <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1"><?= e($error) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Confirmação -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="senha_confirmacao">Confirmação</label>
                        <input
                            id="senha_confirmacao"
                            name="senha_confirmacao"
                            type="password"

                            placeholder="••••••••"
                            class="w-full px-4 py-2 bg-white border <?= errors('senha_confirmacao') ? 'border-red-300 ring-4 ring-red-500/5' : 'border-slate-300' ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-600 transition-all text-sm text-slate-900 placeholder:text-slate-400">
                        <?php if ($error = errors('senha_confirmacao')): ?>
                            <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1"><?= e($error) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-2.5 bg-primary-600 text-white rounded-lg font-bold shadow-sm hover:bg-primary-700 active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-sm">
                        <span>Concluir Cadastro</span>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-slate-500 text-xs">Já possui acesso autorizado?</p>
                <a href="/login" class="inline-block mt-2 text-xs font-bold text-primary-600 hover:text-primary-700 hover:underline">
                    Entrar com Credenciais
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