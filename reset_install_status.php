<?php
/**
 * RESET INSTALL STATUS - Resetta lo stato dell'installazione a idle
 */
header('Content-Type: application/json');

$statusFile = __DIR__ . '/install_status.json';

// Resetta a stato idle
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
