<?php

namespace App\Controllers\Juiz;

use App\Services\Juiz\AvaliacaoService;
use App\Models\Competicao;
use App\Models\Inscricao;
use App\Models\Prova;
use App\Models\JuradoDesignacao;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;
use Core\Exceptions\ValidationException;

#[Middleware(['auth', 'role:jurado'])]
class AvaliacaoController
{
    protected AvaliacaoService $service;

    public function __construct(AvaliacaoService $service)
    {
        $this->service = $service;
    }

    /**
     * Lista competições e provas atribuídas ao juiz logado.
     */
    #[Get('/juiz/dashboard', name: 'juiz.dashboard')]
    public function dashboard()
    {
        $juradoId = session('user')['id'];
        $competicoes = $this->service->getCompeticoesDoJurado($juradoId);

        return view('juiz/dashboard', [
            'title' => 'Painel de Avaliação',
            'competicoes' => $competicoes
        ]);
    }

    /**
     * Lista os atletas de uma prova para avaliação.
     */
    #[Get('/juiz/avaliar/{prova_id}', name: 'juiz.avaliar')]
    public function avaliar(int $prova_id)
    {
        $juradoId = session('user')['id'];
        
        $prova = (new Prova())->with(['competicao', 'inscricoes'])->where('id', '=', $prova_id)->first();
        
        if (!$prova) {
            return Response::makeRedirect('/juiz/dashboard');
        }

        // Verificar designação
        $designacao = (new JuradoDesignacao())
            ->where('usuario_id', '=', $juradoId)
            ->where('prova_id', '=', $prova_id)
            ->first();

        if (!$designacao) {
            return Response::makeRedirect('/juiz/dashboard');
        }

        // Buscar atleta atual da vez (fila)
        $atletaAtivo = $this->service->getAtletaAtivo($prova_id);
        
        // Buscar todas as inscrições para mostrar o progresso/fila
        $inscricoes = (new Inscricao())
            ->where('prova_id', '=', $prova_id)
            ->whereIn('status', ['confirmada', 'pendente'])
            ->orderBy('ordem_apresentacao', 'ASC')
            ->orderBy('id', 'ASC')
            ->with(['atleta.equipe', 'notaPorJurado' => function($db) use ($juradoId) {
                return $db->where('jurado_id', '=', $juradoId);
            }])
            ->get();

        // Verificar se este juiz já avaliou o atleta ATIVO
        $jaAvaliouAtivo = false;
        if ($atletaAtivo) {
            foreach ($inscricoes as $ins) {
                if ($ins->id === $atletaAtivo->id && $ins->notaPorJurado) {
                    $jaAvaliouAtivo = true;
                    break;
                }
            }
        }

        return view('juiz/avaliar', [
            'title' => 'Avaliação: ' . str_replace('_', ' ', $prova->aparelho),
            'prova' => $prova,
            'competicao' => $prova->competicao,
            'inscricoes' => $inscricoes,
            'atletaAtivo' => $atletaAtivo,
            'jaAvaliouAtivo' => $jaAvaliouAtivo,
            'designacao' => $designacao
        ]);
    }

    /**
     * Salva a nota via POST.
     */
    #[Post('/juiz/avaliar/{inscricao_id}/salvar', name: 'juiz.salvar_nota')]
    public function salvarNota(int $inscricao_id)
    {
        $juradoId = session('user')['id'];
        $rawValor = request()->get('valor');
        // Normaliza vírgula para ponto e limpa espaços
        $normalizedValor = str_replace(',', '.', trim((string)$rawValor));
        $valor = (float) $normalizedValor;
        
        $observacao = request()->get('observacao');

        try {
            $this->service->registrarNota($juradoId, $inscricao_id, $valor, $observacao);
            
            if (request()->isHtmx()) {
                 // Ao salvar via HTMX, redirecionamos para recarregar a lógica do "Atleta Ativo"
                 // ou retornamos a própria view de avaliação que o htmx vai filtrar com hx-select
                 return $this->avaliar((int) (new \App\Models\Inscricao())->find($inscricao_id)->prova_id);
            }

            session()->flash('success', 'Nota registrada com sucesso!');
            return Response::makeRedirect(request()->header('Referer') ?: '/juiz/dashboard');

        } catch (ValidationException $e) {
            if (request()->isHtmx()) {
                $err = array_values($e->errors)[0] ?? "Erro ao salvar.";
                return new Response('<span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"><i class="fa-solid fa-triangle-exclamation mr-1"></i> ' . e($err) . '</span>');
            }
            throw $e;
        }
    }
}
