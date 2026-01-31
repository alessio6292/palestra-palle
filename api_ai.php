<?php
/**
 * API unificata per tutte le azioni AI: stato installazione, reset, install, stop, download modello, salva modalità, chat.
 * Tutta la logica è gestita in PHP (niente JavaScript per la parte logica).
 */
header('Content-Type: application/json');

$statusFile = __DIR__ . '/install_status.json';
$rawInput = file_get_contents('php://input');
$jsonInput = json_decode($rawInput, true);
$azione = $_GET['azione'] ?? $_POST['azione'] ?? ($jsonInput['azione'] ?? '');
$isFormPost = ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_SERVER['HTTP_X_REQUESTED_WITH']));
function redirectImpostazioni($msg)
{
    header('Location: impostazioni_ai.php?msg=' . urlencode($msg));
    exit;
}

// ---------- GET: stato installazione ----------
if ($azione === 'status' || (empty($azione) && $_SERVER['REQUEST_METHOD'] === 'GET')) {
    if (file_exists($statusFile)) {
        $status = json_decode(file_get_contents($statusFile), true);
        if (isset($status['timestamp']) && (time() - $status['timestamp']) > 300) {
            echo json_encode(['step' => 'timeout', 'progress' => 0, 'message' => '❌ Timeout. Riprova.', 'done' => true, 'error' => true]);
        } else {
            echo json_encode($status);
        }
    } else {
        echo json_encode(['step' => 'idle', 'progress' => 0, 'message' => 'Pronto per installazione', 'done' => false, 'error' => false]);
    }
    exit;
}

// ---------- POST: reset stato ----------
if ($azione === 'reset') {
    file_put_contents($statusFile, json_encode([
        'step' => 'idle',
        'progress' => 0,
        'message' => 'Pronto per installazione',
        'done' => false,
        'error' => false,
        'stop_requested' => false,
        'timestamp' => time()
    ]));
    echo json_encode(['success' => true, 'message' => 'Stato resettato']);
    exit;
}

// ---------- POST: avvia installazione (in background) ----------
if ($azione === 'install') {
    $installScript = __DIR__ . '/install_run.php';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        pclose(popen('start /B php "' . $installScript . '"', 'r'));
    } else {
        exec('php "' . $installScript . '" > /dev/null 2>&1 &');
    }
    $msg = 'Installazione avviata in background. Attendi 5-10 minuti e ricarica la pagina per verificare.';
    if ($isFormPost)
        redirectImpostazioni($msg);
    echo json_encode(['success' => true, 'message' => $msg]);
    exit;
}

// ---------- POST: ferma installazione ----------
if ($azione === 'stop') {
    if (file_exists($statusFile)) {
        $status = json_decode(file_get_contents($statusFile), true);
        $status['stop_requested'] = true;
        $status['message'] = '⏹️ Annullamento in corso...';
        file_put_contents($statusFile, json_encode($status));
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            @shell_exec('taskkill /F /IM powershell.exe 2>nul');
            @shell_exec('taskkill /F /IM OllamaSetup.exe 2>nul');
        }
        $tempInstaller = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'OllamaSetup.exe';
        if (file_exists($tempInstaller))
            @unlink($tempInstaller);
        file_put_contents($statusFile, json_encode([
            'step' => 'stopped',
            'progress' => 0,
            'message' => '⏹️ Installazione annullata dall\'utente',
            'done' => true,
            'error' => true,
            'timestamp' => time()
        ]));
        if ($isFormPost)
            redirectImpostazioni('Installazione fermata.');
        echo json_encode(['success' => true, 'message' => 'Installazione fermata']);
    } else {
        if ($isFormPost)
            redirectImpostazioni('Nessuna installazione in corso.');
        echo json_encode(['success' => false, 'message' => 'Nessuna installazione in corso']);
    }
    exit;
}

// ---------- POST: download modello ----------
if ($azione === 'download') {
    $input = $jsonInput ?: [];
    $model = $input['model'] ?? $_POST['model'] ?? '';
    if (empty($model)) {
        echo json_encode(['success' => false, 'message' => 'Modello non specificato']);
        exit;
    }
    $modelliValidi = ['phi3:mini'];
    if (!in_array($model, $modelliValidi)) {
        echo json_encode(['success' => false, 'message' => 'Modello non valido']);
        exit;
    }
    $output = shell_exec('where ollama 2>&1');
    if (stripos($output ?? '', 'ollama.exe') === false) {
        echo json_encode(['success' => false, 'message' => 'Ollama non installato']);
        exit;
    }
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        pclose(popen('start /B cmd /c "ollama pull ' . escapeshellarg($model) . '"', 'r'));
    } else {
        exec('ollama pull ' . escapeshellarg($model) . ' > /dev/null 2>&1 &');
    }
    $msg = "Download $model avviato in background. Ricarica la pagina tra qualche minuto.";
    if ($isFormPost)
        redirectImpostazioni($msg);
    echo json_encode(['success' => true, 'message' => $msg]);
    exit;
}



// ---------- POST: chat (risposta JSON per richieste AJAX o da form) ----------
if ($azione === 'chat') {
    $input = $jsonInput ?: $_POST;
    $domanda = trim($input['domanda'] ?? $_POST['domanda'] ?? '');
    if (empty($domanda)) {
        echo json_encode(['risposta' => 'Per favore, scrivi una domanda.', 'success' => false]);
        exit;
    }
    try {
        require_once __DIR__ . '/db.php';
        require_once __DIR__ . '/palestra_ai.php';
        $ai = new PalestraAI($db);
        $risposta = $ai->chat($domanda);
        echo json_encode(['risposta' => $risposta, 'success' => true]);
    } catch (Exception $e) {
        echo json_encode(['risposta' => 'Si è verificato un errore. Riprova più tardi.', 'success' => false]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Azione non valida']);
