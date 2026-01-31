<?php
/**
 * Restituisce lo stato attuale dell'installazione AI
 */
header('Content-Type: application/json');

$statusFile = __DIR__ . '/install_status.json';

if (file_exists($statusFile)) {
    $status = json_decode(file_get_contents($statusFile), true);
    
    // Se lo stato è vecchio di più di 5 minuti, considera fallito
    if (isset($status['timestamp']) && (time() - $status['timestamp']) > 300) {
        echo json_encode([
            'step' => 'timeout',
            'progress' => 0,
            'message' => '❌ Timeout - Installazione bloccata. Riprova.',
            'done' => true,
            'error' => true
        ]);
    } else {
        echo json_encode($status);
    }
} else {
    echo json_encode([
        'step' => 'idle',
        'progress' => 0,
        'message' => 'Pronto per installazione',
        'done' => false,
        'error' => false
    ]);
}
