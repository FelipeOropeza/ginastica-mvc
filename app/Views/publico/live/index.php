<?php $this->layout('layouts/live', ['title' => $title]) ?>

<div x-data="{ 
        provas: <?= htmlspecialchars(json_encode($provas)) ?>,
        currentIndex: 0,
        count: <?= count($provas) ?>,
        timer: 10,
        progress: 100,
        
        init() {
            if (this.count > 0) {
                this.loadCurrentProva();
                this.startRotation();
            }
            
            // Escuta atualizações do Mercure (real-time)
            window.addEventListener('live-update', () => {
                this.loadCurrentProva();
            });
        },
        
        startRotation() {
            setInterval(() => {
                if (this.timer > 0) {
                    this.timer--;
                    this.progress = (this.timer / 10) * 100;
                } else {
                    this.next();
                }
            }, 1000);
        },
        
        next() {
            this.currentIndex = (this.currentIndex + 1) % this.count;
            this.timer = 10;
            this.progress = 100;
            this.loadCurrentProva();
        },
        
        get currentProva() {
            return this.provas[this.currentIndex];
        },
        
        loadCurrentProva() {
            if (this.count === 0) return;
            htmx.ajax('GET', '/live/<?= $competicao->id ?>/prova/' + this.currentProva.id, {
                target: '#prova-container',
                swap: 'innerHTML'
            });
        }
    }" 
    class="flex flex-col h-screen overflow-hidden p-6 md:p-10 relative">

    <!-- Ouvinte Mercure simplificado via Helper -->
    <?= mercure_listen('competicao-' . $competicao->id, 'live-update') ?>
    
    <!-- Background Decor (Minimalista) -->
    <div class="fixed top-0 left-0 w-full h-full -z-10 bg-slate-950"></div>
    <div class="fixed top-0 right-0 w-1/2 h-1/2 bg-primary-600/5 rounded-full blur-[140px] -z-10"></div>
    
    <!-- Header -->
    <header class="flex items-center justify-between mb-6 px-2" x-data="{ 
            time: '', 
            date: '',
            updateTime() {
                const now = new Date();
                this.time = now.toLocaleTimeString('pt-BR', { hour12: false });
                this.date = now.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' });
            }
        }" x-init="updateTime(); setInterval(() => updateTime(), 1000)">
        
        <div class="flex items-center gap-5">
            <div class="flex flex-col">
                <h1 class="text-4xl font-outfit font-black text-white tracking-tighter uppercase italic leading-none mb-2">
                    <?= e($competicao->nome) ?>
                </h1>
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1.5 px-2 py-0.5 rounded bg-red-600 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-lg shadow-red-600/20">
                        <span class="w-2 h-2 rounded-full bg-white pulse-live"></span>
                        LIVE
                    </span>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-none border-l border-white/10 pl-3">
                        <?= e($competicao->local) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-10">
            <!-- Timer da Rotação -->
            <div class="flex flex-col items-end">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1.5 opacity-60">Próximo Aparelho</span>
                <div class="flex items-center gap-3">
                    <span x-text="timer + 's'" class="text-2xl font-outfit font-black text-primary-500 font-mono">10s</span>
                    <div class="w-32 h-1 bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-primary-500 transition-all duration-1000 ease-linear" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Content Area -->
    <main class="flex-1 min-h-0 relative">
        <div id="prova-container" class="h-full">
            <div class="h-full flex items-center justify-center">
                <div class="flex flex-col items-center gap-4 text-slate-600">
                    <i class="fa-solid fa-spinner fa-spin text-3xl"></i>
                    <p class="font-black uppercase tracking-widest text-[10px]">Sincronizando Dashboard...</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="mt-6 flex items-center justify-between border-t border-white/5 pt-4">
        <div class="opacity-40">
            <p class="text-[10px] font-bold text-slate-600 uppercase tracking-[0.2em]">Painel de Resultados Oficiais &copy; <?= date('Y') ?></p>
        </div>

        <div class="flex items-center gap-8">
            <div class="opacity-40">
                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest" x-text="'Slide ' + (currentIndex + 1) + ' de ' + count"></span>
            </div>
            
            <!-- Relógio e Data (Movidos para o Rodapé) -->
            <div class="flex flex-col items-end border-l border-white/10 pl-8">
                <span x-text="date" class="text-[9px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-0.5"></span>
                <span x-text="time" class="text-xl font-outfit font-black text-white/80 font-mono tracking-tight leading-none"></span>
            </div>
        </div>
    </footer>
</div>

