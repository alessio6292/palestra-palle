<?php
/**
 * ============================================================
 * 🧠 PALESTRA AI - Sistema Intelligenza Artificiale
 * ============================================================
 * 
 * Questo file gestisce tutte le funzionalità AI del sito tramite Ollama.
 * 
 * COME FUNZIONA IN PAROLE SEMPLICI:
 * - Il sistema rileva l'hardware per informazione.
 * - Si connette a Ollama locale per risposte intelligenti.
 * - In caso di mancata connessione, l'AI è disabilitata.
 * 
 * ============================================================
 */

class PalestraAI
{

    // ========== VARIABILI PRINCIPALI ==========

    private $db;                              // Connessione al database
    private $ollamaAttivo = false;            // True se Ollama è in esecuzione
    private $modelloCorrente = 'phi3:mini';   // Modello AI in uso



    // ========== COSTRUTTORE ==========

    /**
     * Inizializza l'AI
     * 
     * Quando crei un oggetto PalestraAI, automaticamente:
     * 1. Rileva l'hardware del PC
     * 2. Controlla se Ollama è attivo
     * 3. Usa il modello leggero
     * 
     * @param object $database Connessione al database (dalla classe DB)
     */
    public function __construct($database = null)
    {
        $this->db = $database;
        $this->rilevaHardware();      // Passo 1: Che PC hai?
        $this->controllaOllama();     // Passo 2: Ollama funziona?
    }

    // ================================================================
    // SEZIONE 1: RILEVAMENTO HARDWARE
    // ================================================================

    /**
     * Rileva l'hardware del PC (GPU, RAM, CPU)
     * 
     * Usa comandi Windows (wmic) per scoprire:
     * - Quale scheda video hai e quanta VRAM
     * - Quanta RAM totale
     * - Quanti core ha la CPU
     */
    private function rilevaHardware()
    {
        // Funziona solo su Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
            return;

        // --- RILEVA GPU ---
        $output = shell_exec('wmic path win32_VideoController get name,AdapterRAM /format:list 2>&1');
        if ($output) {
            $nomeGPU = '';
            $vramBytes = 0;

            // Legge ogni riga dell'output
            foreach (explode("\n", $output) as $riga) {
                $riga = trim($riga);
                // Cerca "Name=" per il nome della scheda
                if (stripos($riga, 'Name=') === 0) {
                    $nomeGPU = trim(substr($riga, 5));
                }
                // Cerca "AdapterRAM=" per la memoria video
                if (stripos($riga, 'AdapterRAM=') === 0) {
                    $vramBytes = intval(trim(substr($riga, 11)));
                }
            }

            // Controlla se è una GPU dedicata (NVIDIA o AMD)
            if (preg_match('/NVIDIA|AMD|Radeon|GeForce|RTX/i', $nomeGPU)) {
                // Converte bytes in GB
                $vramGB = round($vramBytes / (1024 * 1024 * 1024), 1);

                // Se VRAM è 0, prova metodo alternativo
                if ($vramGB == 0) {
                    $vramGB = $this->rilevaVRAMAlternativo();
                }

                $this->infoGPU = [
                    'presente' => true,
                    'nome' => $nomeGPU,
                    'vram_gb' => $vramGB
                ];
            }
        }

        // Se non ha GPU dedicata, usa quella integrata
        if (!$this->infoGPU) {
            $this->infoGPU = [
                'presente' => false,
                'nome' => 'Integrata',
                'vram_gb' => 0
            ];
        }

        // --- RILEVA RAM ---
        $output = shell_exec('wmic ComputerSystem get TotalPhysicalMemory 2>&1');
        preg_match('/(\d+)/', $output, $match);
        $this->ramMB = round((isset($match[1]) ? intval($match[1]) : 0) / (1024 * 1024));

        // --- RILEVA CPU ---
        $output = shell_exec('wmic cpu get NumberOfCores 2>&1');
        preg_match('/(\d+)/', $output, $match);
        $this->numeroCPU = isset($match[1]) ? intval($match[1]) : 1;
    }

    /**
     * Metodo alternativo per rilevare VRAM
     * Usato quando wmic non funziona (es. su alcune GPU NVIDIA)
     */
    private function rilevaVRAMAlternativo()
    {
        // Prova nvidia-smi (disponibile su GPU NVIDIA)
        $output = shell_exec('nvidia-smi --query-gpu=memory.total --format=csv,noheader,nounits 2>&1');
        if ($output && is_numeric(trim($output))) {
            return round(intval(trim($output)) / 1024, 1);
        }

        // Prova PowerShell
        $output = shell_exec('powershell -Command "Get-WmiObject -Class Win32_VideoController | Select-Object -ExpandProperty AdapterRAM"');
        if ($output && is_numeric(trim($output))) {
            return round(intval(trim($output)) / (1024 * 1024 * 1024), 1);
        }

        return 0;
    }

    // ================================================================
    // SEZIONE 2: GESTIONE OLLAMA
    // ================================================================

    /**
     * Controlla se Ollama è in esecuzione
     * 
     * Verifica se Ollama è in esecuzione sulla porta standard.
     */
    private function controllaOllama()
    {
        // Usa 127.0.0.1 invece di localhost per evitare problemi di risoluzione DNS/IPv6 su Windows
        $ch = curl_init('http://127.0.0.1:11434/api/tags');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5, // Timeout aumentato a 5 secondi per PC lenti
            CURLOPT_CONNECTTIMEOUT => 2
        ]);

        curl_exec($ch);
        $this->ollamaAttivo = (curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200);
        curl_close($ch);
    }

    /**
     * Restituisce info complete sull'hardware rilevato
     */
    public function getHardwareInfo()
    {
        return [
            'gpu' => $this->infoGPU,
            'ram_gb' => round($this->ramMB / 1024, 1),
            'cpu_cores' => $this->numeroCPU
        ];
    }

    /** Controlla se Ollama è installato sul PC */
    public function isOllamaInstalled()
    {
        return stripos(shell_exec('where ollama 2>&1') ?? '', 'ollama.exe') !== false;
    }

    /** Controlla se Ollama è in esecuzione */
    public function isOllamaRunning()
    {
        return $this->ollamaAttivo;
    }

    /** Controlla se un modello è già scaricato */
    public function isModelDownloaded($nomeModello)
    {
        return $this->isOllamaInstalled() &&
            stripos(shell_exec('ollama list 2>&1') ?? '', $nomeModello) !== false;
    }

    // ================================================================
    // SEZIONE 3: CHATBOT
    // ================================================================

    /**
     * Gestisce una domanda dell'utente nel chatbot
     * 
     * @param string $domanda La domanda dell'utente
     * @return string La risposta dell'AI
     */
    public function chat($domanda)
    {
        if (!$this->db)
            return "Errore: Database non connesso";

        if (!$this->ollamaAttivo) {
            return "⚠️ Servizio AI non disponibile. Verifica che Ollama sia in esecuzione.";
        }

        if (!$this->isModelDownloaded($this->modelloCorrente)) {
            return "⚠️ Modello AI non scaricato. Scarica il modello phi3:mini prima di usare l'AI.";
        }

        // Costruisce il contesto con i dati della palestra
        $contesto = $this->costruisciContesto();

        // Usa l'AI vera
        return $this->chatConOllama($domanda, $contesto);
    }

    /**
     * Costruisce un riassunto dei dati della palestra
     * Questo contesto viene passato all'AI per rispondere
     */
    private function costruisciContesto()
    {
        $testo = "PALESTRA PALLE - DATI ATTUALI:\n\n";

        // --- ISCRITTI ---
        $iscritti = $this->db->GetIscritto();
        $testo .= "📋 ISCRITTI: " . count($iscritti) . " totali\n";
        $attivi = array_filter($iscritti, fn($i) => ($i->Stato ?? '') === 'Attivo');
        $testo .= "   - Attivi: " . count($attivi) . "\n";

        // Abbonamenti in scadenza
        $inScadenza = $this->db->GetIscrittiInScadenza(7);
        if (count($inScadenza) > 0) {
            $testo .= "   - ⚠️ In scadenza (7gg): " . count($inScadenza) . "\n";
            foreach ($inScadenza as $s) {
                $testo .= "     • {$s->Nome} {$s->Cognome} (scade: {$s->Data_scadenza})\n";
            }
        }

        // --- CORSI ---
        $corsi = $this->db->GetCorsi();
        $testo .= "\n📚 CORSI: " . count($corsi) . " attivi\n";
        foreach ($corsi as $c) {
            $disponibili = ($c->posti_disponibili ?? 0) - ($c->posti_occupati ?? 0);
            $testo .= "   - {$c->nome_corso}: {$c->posti_occupati}/{$c->posti_disponibili} iscritti";
            if ($disponibili <= 3)
                $testo .= " ⚠️ QUASI PIENO";
            $testo .= "\n";
        }

        // --- STATISTICHE ---
        $testo .= "\n📊 STATISTICHE:\n";
        $totPosti = array_sum(array_map(fn($c) => $c->posti_disponibili ?? 0, $corsi));
        $totOccupati = array_sum(array_map(fn($c) => $c->posti_occupati ?? 0, $corsi));
        $testo .= "   - Occupazione media: " . ($totPosti > 0 ? round($totOccupati / $totPosti * 100) : 0) . "%\n";

        return $testo;
    }

    /**
     * Risponde usando Ollama (AI vera)
     */
    private function chatConOllama($domanda, $contesto)
    {
        $prompt = "Sei l'assistente AI della Palestra Palle. Rispondi in italiano, in modo professionale ma amichevole.\n\n";
        $prompt .= $contesto . "\n\n";
        $prompt .= "DOMANDA UTENTE: $domanda\n\nRISPOSTA (max 150 parole, usa emoji):";

        $risposta = $this->chiamaOllama($prompt, 200);
        return $risposta ?: "⚠️ Errore nella generazione della risposta.";
    }



    // ================================================================
    // SEZIONE 4: FUNZIONI AI (Pure PHP)
    // ================================================================

    /**
     * Genera una descrizione professionale per un corso
     * 
     * @param string $nomeCorso Nome del corso (es. "Yoga")
     * @param string $livello Livello (principiante, intermedio, avanzato)
     * @param int $durata Durata in minuti
     * @return string Descrizione generata
     */
    public function generaDescrizione($nomeCorso, $livello, $durata)
    {
        if (!$this->ollamaAttivo) {
            return "⚠️ Impossibile generare la descrizione: AI offline.";
        }

        $prompt = "Scrivi una descrizione professionale (80 parole max) per un corso di {$nomeCorso}, livello {$livello}, durata {$durata} minuti. Includi benefici e target.";
        return $this->chiamaOllama($prompt, 120) ?: "Errore generazione.";
    }

    /**
     * Genera consigli personalizzati sui corsi per un cliente specifico
     * 
     * @param int $idCliente ID del cliente
     * @return string Consigli generati con recap
     */
    public function generaConsigli($idCliente)
    {
        if (!$this->db) {
            return "Errore: Database non connesso";
        }

        if (!$this->ollamaAttivo) {
            return "⚠️ Impossibile generare i consigli: AI offline.";
        }

        // Recupera dati del cliente
        $cliente = $this->db->GetIscrittoId($idCliente);
        if (!$cliente) {
            return "Cliente non trovato.";
        }

        // Recupera corsi disponibili
        $corsi = $this->db->GetCorsi();

        // Costruisci contesto del cliente
        $contestoCliente = "DATI CLIENTE:\n";
        $contestoCliente .= "- Nome: {$cliente->Nome} {$cliente->Cognome}\n";
        $contestoCliente .= "- Età: " . (isset($cliente->Data_nascita) ? date_diff(date_create($cliente->Data_nascita), date_create('today'))->y : 'N/A') . " anni\n";
        $contestoCliente .= "- Stato abbonamento: {$cliente->Stato}\n";
        $contestoCliente .= "- Data scadenza: {$cliente->Data_scadenza}\n";

        // Corsi disponibili
        $contestoCorsi = "CORSI DISPONIBILI:\n";
        foreach ($corsi as $c) {
            $disponibili = ($c->posti_disponibili ?? 0) - ($c->posti_occupati ?? 0);
            $contestoCorsi .= "- {$c->nome_corso}: livello {$c->livello}, {$c->durata} min, posti disponibili: {$disponibili}\n";
        }

        $prompt = "Sei un consulente fitness della Palestra Palle. Basandoti sui dati del cliente e sui corsi disponibili, fornisci consigli personalizzati sui corsi più adatti. Includi un recap dei corsi consigliati con motivazioni. Rispondi in italiano, max 200 parole, usa emoji.\n\n{$contestoCliente}\n\n{$contestoCorsi}\n\nCONSIGLI:";

        return $this->chiamaOllama($prompt, 250) ?: "Errore generazione consigli.";
    }

    /**
     * Genera un report AI sui dati della palestra
     * 
     * @return string Report generato
     */
    public function generaReport()
    {
        if (!$this->db) {
            return "Errore: Database non connesso";
        }

        if (!$this->ollamaAttivo) {
            return "⚠️ Impossibile generare il report: AI offline.";
        }

        // Costruisci contesto completo
        $contesto = $this->costruisciContesto();

        $prompt = "Sei l'analista AI della Palestra Palle. Basandoti sui dati forniti, genera un report sintetico sulle performance della palestra, includendo statistiche chiave, trend e suggerimenti per migliorare. Usa paragrafi separati per sezioni, emoji appropriati e mantieni un tono professionale. Rispondi in italiano, max 300 parole.\n\n{$contesto}\n\nREPORT:";

        return $this->chiamaOllama($prompt, 350) ?: "Errore generazione report.";
    }



    // ================================================================
    // SEZIONE 5: FUNZIONI HELPER (Ausiliarie)
    // ================================================================

    // Funzione privata per chiamare l'API di Ollama
    private function chiamaOllama($prompt, $maxToken = 150)
    {
        if (!$this->ollamaAttivo)
            return '';

        $dati = [
            'model' => $this->modelloCorrente,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => 0.7,
                'num_predict' => $maxToken
            ]
        ];

        $url = 'http://127.0.0.1:11434/api/generate'; // Usa IP diretto per evitare problemi DNS
        $tentativi = 0;
        $maxTentativi = 3;

        while ($tentativi < $maxTentativi) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($dati),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT => 30, // Timeout standard
                CURLOPT_CONNECTTIMEOUT => 5 // Timeout connessione
            ]);

            $risposta = curl_exec($ch);
            $err = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && $risposta) {
                $json = json_decode($risposta, true);
                return trim($json['response'] ?? '');
            }

            $tentativi++;
            sleep(1); // Aspetta 1 secondo prima di riprovare
        }

        return ''; // Ha fallito dopo 3 tentativi
    }

    /**
     * Restituisce info complete sul sistema AI
     */
    public function getSystemInfo()
    {
        return [
            'model' => $this->modelloCorrente,
            'ollama_running' => $this->ollamaAttivo,
            'ollama_installed' => $this->isOllamaInstalled(),
            'hardware' => $this->getHardwareInfo()
        ];
    }
}
