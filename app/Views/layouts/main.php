<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'GymPodium' ?> - Sistema de Ginástica</title>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@2.0.2"></script>
    
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
                        gym: {
                            gold: '#FFD700',
                            silver: '#C0C0C0',
                            bronze: '#CD7F32',
                            dark: '#0f172a'
                        }
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
            .btn {
                @apply inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 active:scale-95;
            }
            .btn-primary {
                @apply bg-primary-600 text-white hover:bg-primary-700 shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50;
            }
            .btn-white {
                @apply bg-white text-slate-900 border border-slate-200 hover:bg-slate-50 shadow-sm;
            }
            .card {
                @apply bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden;
            }
            .glass {
                @apply bg-white/70 backdrop-blur-md border border-white/20;
            }
        }
        
        .htmx-indicator {
            display: none;
        }
        .htmx-request .htmx-indicator {
            display: block;
        }
        .htmx-request.htmx-indicator {
            display: block;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            @apply bg-slate-50;
        }
        ::-webkit-scrollbar-thumb {
            @apply bg-slate-300 rounded-full hover:bg-slate-400 transition-colors;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-height-screen">

    <?php $this->include('partials/navbar') ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php $this->renderSection('content') ?>
    </main>

    <?php $this->include('partials/footer') ?>

    <!-- Loading Indicator Global -->
    <div id="loading" class="htmx-indicator fixed top-0 left-0 w-full h-1 bg-primary-500 z-50 overflow-hidden">
        <div class="h-full bg-primary-300 animate-[loading_1s_infinite]"></div>
    </div>

    <style>
        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>

    <script>
        document.body.addEventListener('htmx:beforeSwap', function(evt) {
            if (evt.detail.xhr.status === 422) {
                // Permite trocar conteúdo mesmo em erro de validação (para mostrar erros no form)
                evt.detail.shouldSwap = true;
                evt.detail.isError = false;
            }
        });
    </script>
</body>
</html>
