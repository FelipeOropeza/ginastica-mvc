<?php
/**
 * Partial view para o badge de status da competição.
 * Substituiu o HTML inline que estava hardcoded no CompetitionController::status().
 *
 * Variáveis esperadas:
 * @var string $status   Status atual da competição
 * @var int    $id       ID da competição
 * @var string $classe   Classes CSS do badge
 * @var string $label    Label legível do status
 */
?>
<button @click="open = !open; $event.stopPropagation()"
        id="status-badge-<?= $id ?>"
        class="px-2 py-1 rounded-lg border text-[9px] uppercase tracking-tighter font-black transition-all hover:brightness-95 flex items-center gap-1.5 shadow-sm <?= $classe ?>">
    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>
    <?= e($label) ?>
    <i class="fa-solid fa-chevron-down opacity-30"></i>
</button>
