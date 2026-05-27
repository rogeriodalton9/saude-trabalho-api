<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agente;
use App\Models\Lista;
use App\Models\SaudeDano;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class ImportPortariaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dou:import-portaria';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa dados da Portaria GM/MS nº 5.674 do DOU';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando importação da Portaria 5.674 do DOU...');

        try {
            $client = new Client();
            $url = 'https://www.in.gov.br/web/dou/-/portaria-gm/ms-n-5.674-de-1-de-novembro-de-2024-594040700';
            
            $this->info('Acessando página: ' . $url);
            $crawler = $client->request('GET', $url);

            // Extrai o conteúdo de texto da página
            $content = $crawler->text();

            // Processa as listas
            $this->processarListas($content);

            // Processa os agentes e relacionamentos
            $this->processarAgentes($content);

            $this->info('✅ Importação concluída com sucesso!');

        } catch (\Exception $e) {
            $this->error('❌ Erro durante importação: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Processa e importa as listas
     */
    private function processarListas($content)
    {
        $this->info('Processando listas...');

        // Padrão para encontrar listas (LISTA A, LISTA B, etc)
        preg_match_all('/LISTA\s+([A-Z])\s*-\s*([^LISTA]*?)(?=LISTA|$)/i', $content, $matches);

        if (empty($matches[0])) {
            $this->warn('Nenhuma lista encontrada no padrão esperado.');
            return;
        }

        for ($i = 0; $i < count($matches[1]); $i++) {
            $letra = $matches[1][$i];
            $descricao = trim($matches[2][$i]);

            $nome = "LISTA {$letra} - " . substr($descricao, 0, 100);

            Lista::firstOrCreate(
                ['nome' => $nome],
                ['nome' => $nome]
            );

            $this->info("✓ Lista '{$letra}' importada");
        }
    }

    /**
     * Processa e importa os agentes
     */
    private function processarAgentes($content)
    {
        $this->info('Processando agentes e relacionamentos...');

        // Padrão para encontrar CID-10 (ex: A00, B01, J30, etc)
        preg_match_all('/([A-Z]\d{2}(?:\.\d)?)\s*-\s*([^A-Z\d]*?)(?=[A-Z]\d{2}|$)/i', $content, $matches);

        if (empty($matches[0])) {
            $this->warn('Nenhum CID-10 encontrado.');
            return;
        }

        $lista = Lista::first(); // Obtém a primeira lista

        if (!$lista) {
            $this->error('Nenhuma lista disponível para relacionar agentes.');
            return;
        }

        for ($i = 0; $i < count($matches[1]); $i++) {
            $cid = $matches[1][$i];
            $risco = trim($matches[2][$i]);

            // Extrai nome do agente (primeiras palavras do risco)
            $agente_nome = substr($risco, 0, 80) ?: 'Agente Desconhecido';

            // Cria ou obtém o agente
            $agente = Agente::firstOrCreate(
                ['nome' => $agente_nome],
                ['nome' => $agente_nome]
            );

            // Cria o relacionamento na tabela saude_danos
            SaudeDano::firstOrCreate(
                [
                    'CID10' => $cid,
                    'lista_id' => $lista->id,
                    'agente_id' => $agente->id,
                ],
                [
                    'CID10' => $cid,
                    'lista_id' => $lista->id,
                    'agente_id' => $agente->id,
                    'risco' => $risco,
                ]
            );

            $this->info("✓ CID {$cid} vinculado a agente");
        }

        $this->info('✓ Agentes e relacionamentos importados');
    }
}
