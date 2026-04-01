<?php $this->layout('layouts/admin', ['title' => $title]) ?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold border-l-4 border-indigo-500 pl-3"><?= e($title) ?></h1>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-4 bg-gray-50 border-b border-gray-200">
        <p class="text-gray-600 text-sm">
            Pergunte em linguagem natural sobre as competições, notas e resultados. O assistente usará IA para buscar os dados em tempo real no banco e te dar uma resposta amigável.
        </p>
        <div class="mt-3 flex flex-wrap gap-2 text-xs">
            <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded cursor-pointer hover:bg-indigo-200 transition" onclick="document.getElementById('pergunta').value=this.innerText">Quais atletas tiveram a maior nota final no geral?</span>
            <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded cursor-pointer hover:bg-indigo-200 transition" onclick="document.getElementById('pergunta').value=this.innerText">Qual aparelho costuma ter as menores notas?</span>
            <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded cursor-pointer hover:bg-indigo-200 transition" onclick="document.getElementById('pergunta').value=this.innerText">Quantas inscrições temos em competições abertas?</span>
        </div>
    </div>

    <div class="p-6">
        <div id="chat-container" class="space-y-4 mb-6 max-h-[500px] overflow-y-auto">
            <!-- As mensagens aparecerão aqui -->
            <div class="bg-gray-100 border border-gray-200 text-gray-800 p-4 rounded-lg rounded-tl-none w-3/4">
                <strong>Assistente:</strong><br>
                <div class="mt-1">
                    Olá! Sou seu Assistente de IA de Ginástica.<br>
                    O que você gostaria de saber sobre nossos atletas, competições ou resultados hoje?
                </div>
            </div>
        </div>

        <form 
            hx-post="<?= route('admin.assistente.perguntar') ?>" 
            hx-target="#chat-container" 
            hx-swap="beforeend"
            hx-indicator="#loading"
            class="flex items-center gap-2"
            hx-on::after-request="this.reset(); setTimeout(() => { const container = document.getElementById('chat-container'); container.scrollTop = container.scrollHeight; }, 100);"
        >
            <?= csrf_field() ?>
            <input 
                type="text" 
                id="pergunta" 
                name="pergunta" 
                class="flex-1 p-3 border border-gray-300 rounded focus:border-indigo-500 focus:ring focus:ring-indigo-200 outline-none" 
                placeholder="Digite sua pergunta..." 
                required
            />
            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700 transition flex items-center justify-center font-medium">
                <i class="fas fa-paper-plane mr-2"></i> Perguntar
            </button>
        </form>

        <div id="loading" class="htmx-indicator mt-4 text-center text-indigo-600 font-medium">
            <i class="fas fa-circle-notch fa-spin mr-2"></i> A inteligência artificial está analisando os dados...
        </div>
    </div>
</div>

<style>
.htmx-indicator {
    display: none;
}
.htmx-request .htmx-indicator {
    display: block;
}
.htmx-request.htmx-indicator {
    display: block;
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
