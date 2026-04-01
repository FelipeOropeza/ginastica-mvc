<div class="flex flex-col gap-4 mt-4 animate-fade-in mb-4">
    <!-- Mensagem do Usuário -->
    <div class="bg-indigo-100 border border-indigo-200 text-indigo-900 p-3 rounded-lg rounded-tr-none w-3/4 self-end ml-auto">
        <strong>Você:</strong><br>
        <div class="mt-1"><?= e($pergunta) ?></div>
    </div>

    <!-- Mensagem do Assistente -->
    <div class="bg-gray-100 border border-gray-200 text-gray-800 p-4 rounded-lg rounded-tl-none w-3/4 overflow-x-auto shadow-sm">
        <strong>Assistente:</strong><br>
        <div class="mt-2 text-gray-700 whitespace-pre-wrap leading-relaxed">
            <?= $resposta ?>
        </div>
    </div>
</div>
