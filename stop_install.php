<?php
/**
 * STOP INSTALL - Ferma l'installazione AI in corso
 */
header('Content-Type: application/json');

$statusFile = __DIR__ . '/install_status.json';

// Leggi lo stato attuale
if (file_exists($statusFile)) {
    $status = json_decode(file_get_contents($statusFile), true);
    
    // Imposta flag di stop
    $status['stop_requested'] = true;
    $status['message'] = '⏹️ Annullamento in corso...';
    
    file_put_contents($statusFile, json_encode($status));
    
    // Prova a terminare i processi di download/installazione
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Termina eventuali processi PowerShell di download
        shell_exec('taskkill /F /IM powershell.exe 2>nul');
        // Termina eventuali installer di Ollama
        shell_exec('taskkill /F /IM OllamaSetup.exe 2>nul');
    }
    
    // Pulisci file temporanei
    $tempInstaller = sys_get_temp_dir() . '\\OllamaSetup.exe';
    if (file_exists($tempInstaller)) {
        @unlink($tempInstaller);
    }
    
    // Aggiorna stato finale
    file_put_contents($statusFile, json_encode([
        'step' => 'stopped',
        'progress' => 0,
        'message' => '⏹️ Installazione annullata dall\'utente',
        'done' => true,
        'error' => true,
        'stopped' => true,
        'timestamp' => time()
    ]));
    
    echo json_encode(['success' => true, 'message' => 'Installazione fermata']);
} else {
    echo json_encode(['success' => false, 'message' => 'Nessuna installazione in corso']);
}
