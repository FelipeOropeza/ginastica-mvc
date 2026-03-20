<div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex justify-center items-center px-4" id="atleta-modal">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 overflow-hidden transform transition-all">
        <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 backdrop-blur-md">
            <div>
                <h3 class="font-outfit font-black text-xl text-slate-800 tracking-tight">Vincular Técnico</h3>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-none mt-1"><?= e($atleta->nome_completo ?? 'Atleta') ?></p>
            </div>
            <button onclick="document.getElementById('modal-container').innerHTML = ''" class="text-slate-300 hover:text-slate-600 hover:rotate-90 transition-all duration-300">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <form hx-post="<?= route('admin.atletas.update', ['id' => $atleta->id]) ?>" hx-target="#form-feedback" hx-swap="innerHTML" class="p-8">
            <div id="form-feedback" class="mb-4"></div>
            
            <div class="space-y-6">
                <!-- Nome Completo -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Nome Completo</label>
                    <input type="text" name="nome_completo" value="<?= e($atleta->nome_completo) ?>" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
                </div>

                <!-- CPF -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Documento (CPF)</label>
                    <input type="text" name="cpf" value="<?= e($atleta->cpf) ?>" placeholder="000.000.000-00"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
                </div>

                <!-- Seleção de Equipe -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Equipe de Pertencimento</label>
                    <div class="relative group">
                        <select name="equipe_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all appearance-none outline-none">
                            <option value="">-- Selecionar Equipe --</option>
                            <?php foreach ($equipes as $eq): ?>
                                <option value="<?= $eq->id ?>" <?= $atleta->equipe_id == $eq->id ? 'selected' : '' ?>><?= e($eq->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none group-hover:text-primary-500 transition-colors"></i>
                    </div>
                </div>

                <!-- Seleção de Categoria -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Graduação/Categoria Técnica</label>
                    <div class="relative group">
                        <select name="categoria_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all appearance-none outline-none">
                            <option value="">-- Selecionar Categoria --</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat->id ?>" <?= $atleta->categoria_id == $cat->id ? 'selected' : '' ?>><?= e($cat->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none group-hover:text-primary-500 transition-colors"></i>
                    </div>
                </div>

                <!-- Status de Ativação -->
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Status de Disponibilidade</p>
                        <p class="text-xs font-bold text-slate-700">O atleta pode se inscrever em provas?</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="ativo" value="1" class="sr-only peer" <?= $atleta->ativo ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                </div>
            </div>
            
            <div class="flex flex-col gap-3 mt-8">
                <button type="submit" class="w-full btn bg-primary-600 text-white hover:bg-primary-700 shadow-lg shadow-primary-600/30 flex items-center justify-center gap-2 py-3 rounded-xl font-black uppercase text-xs tracking-[0.15em] transition-all transform active:scale-95">
                    Salvar Alterações <i class="fa-solid fa-cloud-arrow-up text-[10px]"></i>
                </button>
                <button type="button" onclick="document.getElementById('modal-container').innerHTML = ''" class="w-full py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                    Cancelar Edição
                </button>
            </div>
        </form>
    </div>
</div>
