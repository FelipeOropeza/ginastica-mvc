<?php

namespace App\Services;

class GeminiService
{
    private string $apiKey;
    private string $apiUrl;
    private bool $useOllama = false;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        
        if (empty($this->apiKey)) {
            $this->useOllama = true;
            // Para acessar o Ollama instalado no Windows/Mac a partir do Docker
            $this->apiUrl = 'http://host.docker.internal:11434/api/generate';
        } else {
            $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
        }
    }

    public function generateText(string $prompt, string $systemInstruction = ''): string
    {
        return $this->useOllama 
            ? $this->callOllama($prompt, $systemInstruction)
            : $this->callGemini($prompt, $systemInstruction);
    }

    private function callGemini(string $prompt, string $systemInstruction): string
    {
        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => ['temperature' => 0.1]
        ];

        if (!empty($systemInstruction)) {
            $payload['system_instruction'] = ['parts' => [['text' => $systemInstruction]]];
        }

        return $this->request("{$this->apiUrl}?key={$this->apiKey}", $payload, 'candidates.0.content.parts.0.text');
    }

    private function callOllama(string $prompt, string $systemInstruction): string
    {
        $fullPrompt = !empty($systemInstruction) ? "System: $systemInstruction\nUser: $prompt" : $prompt;
        
        $payload = [
            'model' => env('OLLAMA_MODEL', 'llama3'),
            'prompt' => $fullPrompt,
            'stream' => false,
            'options' => ['temperature' => 0.1]
        ];

        return $this->request($this->apiUrl, $payload, 'response');
    }

    private function request(string $url, array $payload, string $dataKey): string
    {
        // Aumenta o tempo de execução do PHP para 2 minutos para suportar IAs locais lentas
        set_time_limit(120);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Timeout de conexão e de resposta
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch) || $httpCode >= 400) {
            $error = curl_error($ch);
            logger()->error("Erro IA ({$url} - HTTP {$httpCode}): " . ($error ?: $response));
            throw new \Exception("Falha na comunicação com a IA.");
        }

        $data = json_decode($response, true);
        
        // Helper simples para pegar chave aninhada tipo 'a.b.c'
        $keys = explode('.', $dataKey);
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data = $data[$key];
            } else {
                return '';
            }
        }

        return is_string($data) ? $data : '';
    }
}
