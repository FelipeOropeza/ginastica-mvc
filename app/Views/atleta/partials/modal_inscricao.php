<!-- Overlay -->
<div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex justify-center items-center px-4" id="inscricao-modal-bg">
    <!-- Modal -->
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg border border-slate-200 overflow-hidden transform transition-all" @click.stop="">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <div>
                <h3 class="font-outfit font-bold text-lg text-slate-900">Inscrição em Prova</h3>
                <p class="text-xs text-slate-500"><?= htmlspecialchars($competicao->nome) ?></p>
            </div>
            <button onclick="document.getElementById('modal-container').innerHTML = ''" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <form hx-post="<?= route('atleta.competicoes.store') ?>" hx-target="#form-feedback" hx-swap="innerHTML" class="p-6">
            <input type="hidden" name="competicao_id" value="<?= $competicao->id ?>">
            
            <div id="form-feedback" class="mb-2"></div>
            
            <p class="text-sm text-slate-700 mb-4 font-medium italic">
                Sua categoria: <span class="text-primary-600 font-bold"><?= e($atleta->categoria->nome ?? 'S/C') ?></span>
            </p>
            
            <div class="space-y-3 mb-6 max-h-64 overflow-y-auto pr-2">
                <?php if (empty($provas)): ?>
                    <div class="text-center p-8 bg-amber-50 rounded-xl border border-dashed border-amber-300">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500 mb-4 text-3xl"></i>
                        <p class="text-sm text-amber-800 font-bold">Nenhuma Prova Disponível</p>
                        <p class="text-[11px] text-amber-700">Não há provas cadastradas para sua categoria nesta competição.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($provas as $prova): ?>
                        <?php 
                            $jaInscrito = in_array($prova->id, $inscritoIds);
                            $vagasRestantes = $prova->max_participantes > 0 ? ($prova->max_participantes - $prova->inscritos_count) : null;
                            $cheio = ($vagasRestantes !== null && $vagasRestantes <= 0);
                            $disabled = ($jaInscrito || $cheio);
                        ?>
                        <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg <?= $disabled ? 'opacity-60 grayscale cursor-not-allowed bg-slate-50' : 'cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-all has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500' ?>">
                            <div class="flex items-center h-5">
                                <?php if ($jaInscrito): ?>
                                    <i class="fa-solid fa-circle-check text-green-500"></i>
                                <?php elseif ($cheio): ?>
                                    <i class="fa-solid fa-circle-xmark text-red-400"></i>
                                <?php else: ?>
                                    <input type="checkbox" name="provas_id[]" value="<?= $prova->id ?>" 
                                           class="w-4 h-4 text-primary-600 border-primary-300 rounded focus:ring-primary-500 accent-primary-600">
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0 flex flex-col">
                                <span class="text-sm font-bold text-slate-900 flex items-center justify-between">
                                    <?= ucfirst(str_replace('_', ' ', $prova->aparelho)) ?>
                                    <?php if ($jaInscrito): ?>
                                        <span class="text-[9px] font-bold text-green-600 uppercase">Confirmado</span>
                                    <?php elseif ($cheio): ?>
                                        <span class="text-[9px] font-bold text-red-500 uppercase">Esgotado</span>
                                    <?php elseif ($vagasRestantes !== null): ?>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase"><?= $vagasRestantes ?> vagas</span>
                                    <?php endif; ?>
                                </span>
                                <?php if($prova->descricao): ?>
                                    <span class="text-[10px] text-slate-500 leading-tight mt-1"><?= htmlspecialchars($prova->descricao) ?></span>
                                <?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-container').innerHTML = ''" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-all">
                    Fechar
                </button>
                <?php if (!empty($provas)): ?>
                <button type="submit" class="px-4 py-2 text-sm font-semibold bg-primary-600 text-white rounded-lg hover:bg-primary-700 shadow-sm border border-primary-500 transition-all focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Inscrever-se
                </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
