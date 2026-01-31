<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/palestra_ai.php';
$ai = new PalestraAI($db);
$hwInfo = $ai->getHardwareInfo();
$ollamaInstallato = $ai->isOllamaInstalled();
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>⚙️ Configurazione AI - Palestra Palle</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .settings-container { max-width: 700px; margin: 40px auto; padding: 30px; }
        .hardware-info { background: var(--dark-primary); padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid var(--yellow-neon); }
        .hardware-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--dark-tertiary); }
        .hardware-item:last-child { border-bottom: none; }
        .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; }
        .status-ok { background: #00ff88; color: var(--dark-primary); }
        .status-error { background: #ff4444; color: white; }
        .install-btn { width: 100%; padding: 15px; background: linear-gradient(135deg, #00ff88, #00cc70); color: var(--dark-primary); border: none; border-radius: 8px; font-size: 1rem; font-weight: bold; cursor: pointer; margin: 10px 0; }
        .install-btn:hover { transform: translateY(-2px); }
        .install-btn.danger { background: linear-gradient(135deg, #ff4444, #cc0000); color: white; }
        .msg-box { background: rgba(0, 255, 136, 0.2); border: 2px solid #00ff88; border-radius: 8px; padding: 15px; margin-bottom: 20px; color: #00ff88; }
    </style>
</head>
<body>
    <h1>⚙️ Configurazione AI</h1>
    <div class="menu">
        <a href="Index.php">ISCRITTI</a>
        <a href="corsi.php">CORSI</a>
        <a href="gestione.php">GESTIONE CORSI</a>
        <a href="ai_dashboard.php">🧠 AI</a>
    </div>

    <div class="settings-container">
        <?php if ($msg !== ''): ?>
            <div class="msg-box"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <div class="hardware-info">
            <h3 style="color: var(--yellow-neon); margin-bottom: 15px;">🔍 Hardware Rilevato</h3>
            <div class="hardware-item"><strong>GPU:</strong> <span><?= htmlspecialchars($hwInfo['gpu']['nome']) ?></span></div>
            <?php if (!empty($hwInfo['gpu']['presente'])): ?>
                <div class="hardware-item"><strong>VRAM:</strong> <span><?= (int)$hwInfo['gpu']['vram_gb'] ?>GB</span></div>
            <?php endif; ?>
            <div class="hardware-item"><strong>RAM:</strong> <span><?= (float)$hwInfo['ram_gb'] ?>GB</span></div>
            <div class="hardware-item"><strong>CPU Cores:</strong> <span><?= (int)$hwInfo['cpu_cores'] ?></span></div>
            <div class="hardware-item"><strong>Ollama:</strong>
                <span><?php if ($ollamaInstallato): ?><span class="status-badge status-ok">✓ Installato</span><?php else: ?><span class="status-badge status-error">✗ Non installato</span><?php endif; ?></span>
            </div>
        </div>


        <?php if (!$ollamaInstallato): ?>
            <h3 style="color: #ff4444; margin-bottom: 15px;">⚠️ Setup Richiesto</h3>
            <p style="color: #ccc; margin-bottom: 15px;">Ollama non è installato. Avvia l'installazione (in background, 5-10 minuti):</p>
            <form method="POST" action="api_ai.php">
                <input type="hidden" name="azione" value="install">
                <button type="submit" class="install-btn">🚀 Installa AI (Auto-Setup)</button>
            </form>
            <form method="POST" action="api_ai.php">
                <input type="hidden" name="azione" value="stop">
                <button type="submit" class="install-btn danger">⏹️ Ferma Installazione</button>
            </form>
            <small style="color: #888; display: block; margin-top: 10px;">Download ~55MB + Modello ~2.3GB</small>
        <?php else:
            $phi3Installed = $ai->isModelDownloaded('phi3');
        ?>
            <h3 style="color: var(--yellow-neon); margin-bottom: 15px;">📦 Modello AI</h3>
            <div style="margin-bottom: 15px; padding: 15px; background: var(--dark-primary); border-radius: 8px;">
                <strong>phi3:mini:</strong>
                <?php if ($phi3Installed): ?>
                    <span class="status-badge status-ok">✓ Scaricato</span>
                <?php else: ?>
                    <span class="status-badge status-error">✗ Mancante</span>
                    <form method="POST" action="api_ai.php" style="margin-top: 10px;">
                        <input type="hidden" name="azione" value="download">
                        <input type="hidden" name="model" value="phi3:mini">
                        <button type="submit" class="install-btn">⬇️ Scarica phi3:mini (2.3GB)</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>



        <p style="margin-top: 30px;"><a href="ai_dashboard.php" style="padding: 12px 24px; background: var(--brown-medium); color: white; border-radius: 8px; text-decoration: none;">← Torna alla Dashboard AI</a></p>
    </div>
</body>
</html>
