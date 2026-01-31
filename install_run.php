<?php
/**
 * Esecuzione installazione Ollama (chiamato in background da api_ai.php)
 */
header('Content-Type: application/json');
set_time_limit(600);
ignore_user_abort(true);

$statusFile = __DIR__ . '/install_status.json';

function updateStatus($step, $progress, $message, $done = false, $error = false) {
    global $statusFile;
    $existing = [];
    if (file_exists($statusFile)) {
        $existing = json_decode(file_get_contents($statusFile), true) ?: [];
    }
    file_put_contents($statusFile, json_encode([
        'step' => $step, 'progress' => $progress, 'message' => $message,
        'done' => $done, 'error' => $error, 'timestamp' => time(),
        'stop_requested' => $existing['stop_requested'] ?? false
    ]));
}

function checkStopRequested() {
    global $statusFile;
    if (file_exists($statusFile)) {
        $status = json_decode(file_get_contents($statusFile), true);
        if (!empty($status['stop_requested'])) {
            updateStatus('stopped', 0, '⏹️ Installazione annullata dall\'utente', true, true);
            exit;
        }
    }
}

updateStatus('check', 5, '🔍 Verifico se Ollama è già installato...');
$ollamaPath = getenv('LOCALAPPDATA') . '\\Programs\\Ollama\\ollama.exe';

if (file_exists($ollamaPath)) {
    shell_exec('start /B "' . $ollamaPath . '" serve >nul 2>&1');
    sleep(3);
    updateStatus('done', 100, '✅ Ollama era già installato! Servizio avviato.', true);
    exit;
}

updateStatus('download', 10, '⬇️ Scarico Ollama da internet...');
$pathDownload = sys_get_temp_dir() . '\\OllamaSetup.exe';
$urlDownload = 'https://ollama.com/download/OllamaSetup.exe';
checkStopRequested();
if (file_exists($pathDownload)) unlink($pathDownload);

$cmd = 'powershell -Command "& { [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; $ProgressPreference = \"SilentlyContinue\"; Invoke-WebRequest -Uri \'' . $urlDownload . '\' -OutFile \'' . $pathDownload . '\' }"';
$proc = popen($cmd . ' 2>&1', 'r');
$lastSize = 0;
$expectedSize = 55000000;
$startTime = time();
$timeout = 180;

while (!feof($proc) && (time() - $startTime) < $timeout) {
    if (file_exists($statusFile)) {
        $cur = json_decode(file_get_contents($statusFile), true);
        if (!empty($cur['stop_requested'])) {
            pclose($proc);
            @unlink($pathDownload);
            updateStatus('stopped', 0, '⏹️ Download annullato', true, true);
            exit;
        }
    }
    if (file_exists($pathDownload)) {
        $sz = filesize($pathDownload);
        if ($sz != $lastSize) {
            $lastSize = $sz;
            $pct = min(40, 10 + (int)(($sz / $expectedSize) * 30));
            updateStatus('download', $pct, '⬇️ Scaricando... ' . round($sz / 1048576, 1) . ' MB');
        }
    }
    usleep(500000);
}
pclose($proc);

clearstatcache();
if (!file_exists($pathDownload) || filesize($pathDownload) < 1000000) {
    updateStatus('error', 0, '❌ Download fallito. Riprova.', true, true);
    exit;
}
updateStatus('download_done', 45, '✅ Download completato', false, false);
sleep(1);
checkStopRequested();

updateStatus('install', 50, '🔧 Installazione in corso...');
shell_exec('"' . $pathDownload . '" /S');
$tentativi = 0;
while ($tentativi < 40) {
    sleep(3);
    $tentativi++;
    if (file_exists($statusFile)) {
        $cur = json_decode(file_get_contents($statusFile), true);
        if (!empty($cur['stop_requested'])) {
            updateStatus('stopped', 0, '⏹️ Installazione annullata', true, true);
            exit;
        }
    }
    updateStatus('install', 50 + (int)(($tentativi / 40) * 30), "🔧 Installazione... ({$tentativi}s)");
    if (file_exists($ollamaPath)) break;
}

if (!file_exists($ollamaPath)) {
    updateStatus('error', 0, '❌ Installazione fallita. Installa manualmente da ollama.com', true, true);
    exit;
}
updateStatus('install_done', 80, '✅ Ollama installato!');
sleep(1);

updateStatus('start', 85, '🚀 Avvio servizio Ollama...');
shell_exec('start /B "' . $ollamaPath . '" serve >nul 2>&1');
sleep(5);

$ch = curl_init('http://127.0.0.1:11434/api/tags');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5]);
curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 200) {
    updateStatus('error', 0, '❌ Servizio non risponde. Riavvia il PC e riprova.', true, true);
    exit;
}
updateStatus('service_ok', 90, '✅ Servizio Ollama attivo!');
sleep(1);
updateStatus('model', 92, '📦 Scarico modello AI phi3:mini (2.3GB)...');
shell_exec('start /B cmd /c "\"' . $ollamaPath . '\" pull phi3:mini" >nul 2>&1');
sleep(3);
updateStatus('done', 100, '✅ Installazione completata! Il modello si sta scaricando in background.', true);
@unlink($pathDownload);
