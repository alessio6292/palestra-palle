<!DOCTYPE html>
<html>

<head>
    <title>🧠 AI Dashboard - Palestra Palle</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .ai-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .chatbot-card {
            background: linear-gradient(135deg, var(--dark-secondary), var(--dark-tertiary));
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            border: 2px solid var(--yellow-neon);
        }

        .chatbot-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .chatbot-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--yellow-neon), var(--brown-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .chatbot-messages {
            background: var(--dark-primary);
            border-radius: 12px;
            padding: 20px;
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 15px;
            min-height: 150px;
        }

        .chat-message {
            padding: 12px 16px;
            margin: 10px 0;
            border-radius: 12px;
            max-width: 85%;
            line-height: 1.5;
        }

        .chat-message.user {
            background: var(--brown-medium);
            margin-left: auto;
            text-align: right;
        }

        .chat-message.bot {
            background: linear-gradient(135deg, #2a3a2a, #1a2a1a);
            border-left: 3px solid var(--yellow-neon);
        }

        .chat-input-container {
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            padding: 15px;
            background: var(--dark-primary);
            border: 2px solid var(--brown-medium);
            color: var(--white);
            border-radius: 12px;
            font-size: 1rem;
        }

        .chat-send {
            padding: 15px 30px;
            background: linear-gradient(135deg, var(--yellow-neon), var(--brown-light));
            color: var(--dark-primary);
            border: none;
            border-radius: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .chat-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .typing-indicator {
            display: none;
            color: var(--yellow-neon);
            font-style: italic;
            padding: 10px;
        }

        .typing-indicator.active {
            display: block;
        }

        .typing-dots span {
            animation: blink 1.4s infinite both;
        }

        .typing-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 0.2;
            }

            50% {
                opacity: 1;
            }
        }

        .settings-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--yellow-neon), var(--brown-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.4);
            z-index: 10000;
            border: 3px solid var(--brown-dark);
        }

        .settings-btn:hover {
            transform: rotate(90deg) scale(1.1);
        }

        .settings-btn svg {
            width: 32px;
            height: 32px;
            fill: var(--dark-primary);
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .modal-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: var(--dark-secondary);
            border: 3px solid var(--yellow-neon);
            border-radius: 20px;
            padding: 30px;
            max-width: 700px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.9);
            box-sizing: border-box;
        }

        .hardware-info {
            background: var(--dark-primary);
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            border-left: 4px solid var(--yellow-neon);
            max-width: 100%;
            box-sizing: border-box;
        }

        .hardware-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--dark-tertiary);
            flex-wrap: wrap;
            gap: 10px;
        }

        .hardware-item:last-child {
            border-bottom: none;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            white-space: nowrap;
        }

        .status-ok {
            background: #00ff88;
            color: var(--dark-primary);
        }

        .status-error {
            background: #ff4444;
            color: white;
        }

        .install-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #00ff88, #00cc70);
            color: var(--dark-primary);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin: 10px auto;
            transition: all 0.3s;
            box-sizing: border-box;
            display: block;
        }

        .install-btn:hover {
            transform: translateY(-2px);
        }

        .install-btn:disabled {
            background: #666;
            cursor: not-allowed;
        }

        .ai-card {
            background: var(--dark-secondary);
            border: 2px solid var(--brown-medium);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-medium);
        }

        .ai-card h3 {
            color: var(--yellow-neon);
            margin-bottom: 20px;
            font-size: 1.5rem;
            border-bottom: 2px solid var(--brown-medium);
            padding-bottom: 10px;
        }

        .ai-result {
            background: var(--dark-primary);
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid var(--yellow-neon);
        }

        .ai-insight {
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ai-insight.info {
            background: rgba(77, 150, 255, 0.15);
            border-left: 4px solid #4d96ff;
        }

        .ai-insight.success {
            background: rgba(0, 255, 136, 0.15);
            border-left: 4px solid #00ff88;
        }

        .ai-insight.warning {
            background: rgba(255, 165, 0, 0.15);
            border-left: 4px solid #ffa500;
        }

        .ai-form input,
        .ai-form select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            background: var(--dark-primary);
            border: 2px solid var(--brown-medium);
            color: var(--white);
            border-radius: 6px;
            font-size: 1rem;
        }

        .ai-form button {
            background: linear-gradient(135deg, var(--yellow-neon), var(--brown-light));
            color: var(--dark-primary);
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .confidence {
            display: inline-block;
            padding: 4px 12px;
            background: var(--yellow-neon);
            color: var(--dark-primary);
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .recommendation-card {
            background: var(--dark-tertiary);
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid var(--yellow-neon);
        }

        .recommendation-card h4 {
            color: var(--yellow-bright);
            margin-bottom: 8px;
        }

        .recommendation-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .tag {
            background: var(--brown-medium);
            color: var(--white);
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <div class="settings-btn" onclick="openSettings()">
        <svg viewBox="0 0 24 24">
            <path
                d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
        </svg>
    </div>

    <div class="modal-overlay" id="settingsModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeSettings()">×</span>
            <h2 style="color: var(--yellow-neon); margin-bottom: 30px;">⚙️ Configurazione AI</h2>
            <?php
            require('db.php');
            require('palestra_ai.php');
            $ai = new PalestraAI($db);
            $hwInfo = $ai->getHardwareInfo();
            $ollamaInstallato = $ai->isOllamaInstalled();
            ?>
            <div class="hardware-info">
                <h3 style="color: var(--yellow-neon); margin-bottom: 15px;">🔍 Hardware Rilevato</h3>
                <div class="hardware-item"><strong>GPU:</strong> <span><?= $hwInfo['gpu']['nome'] ?></span></div>
                <?php if ($hwInfo['gpu']['presente']): ?>
                    <div class="hardware-item"><strong>VRAM:</strong> <span><?= $hwInfo['gpu']['vram_gb'] ?>GB</span></div>
                <?php endif; ?>
                <div class="hardware-item"><strong>RAM:</strong> <span><?= $hwInfo['ram_gb'] ?>GB</span></div>
                <div class="hardware-item"><strong>CPU Cores:</strong> <span><?= $hwInfo['cpu_cores'] ?></span></div>
                <div class="hardware-item"><strong>Ollama:</strong> <span><?php if ($ollamaInstallato): ?><span
                                class="status-badge status-ok">✓ Installato</span><?php else: ?><span
                                class="status-badge status-error">✗ Non installato</span><?php endif; ?></span></div>
            </div>

            <div
                style="background: var(--dark-primary); padding: 20px; border-radius: 12px; margin: 20px auto; text-align: center;">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">💡</div>
                <strong style="color: var(--yellow-neon); font-size: 1.3rem;">Modalità LIGHT</strong><br>
                <small style="color: #ccc;">CPU • 4GB RAM • phi3:mini</small>
            </div>
            <?php if (!$ollamaInstallato): ?>
                <div style="margin-top: 30px; max-width: 100%; box-sizing: border-box;" id="installSection">
                    <h3 style="color: #ff4444; margin-bottom: 15px;">⚠️ Setup Richiesto</h3>
                    <p style="color: #ccc; margin-bottom: 15px;">Ollama non è installato. Clicca per installare
                        automaticamente:</p>
                    <div id="progressContainer" style="display: none; margin: 20px 0;">
                        <div
                            style="background: var(--dark-primary); border-radius: 10px; padding: 3px; margin-bottom: 10px;">
                            <div id="progressBar"
                                style="height: 25px; background: linear-gradient(90deg, #00ff88, #00cc70); border-radius: 8px; width: 0%; transition: width 0.5s ease; display: flex; align-items: center; justify-content: center;">
                                <span id="progressPercent"
                                    style="color: #111; font-weight: bold; font-size: 0.9rem;">0%</span>
                            </div>
                        </div>
                        <p id="progressMessage"
                            style="color: var(--yellow-neon); text-align: center; font-size: 1rem; margin: 10px 0;">
                            Preparazione...</p>
                    </div>
                    <button class="install-btn" id="installBtn" onclick="installAI()">🚀 Installa AI (Auto-Setup)</button>
                    <button class="install-btn" id="stopBtn" onclick="stopInstall()"
                        style="display: none; background: linear-gradient(135deg, #ff4444, #cc0000);">⏹️ Ferma
                        Installazione</button>
                    <small style="color: #888; display: block; margin-top: 10px; text-align: center;">Download ~55MB +
                        Modello ~2.3GB • Richiede 5-10 minuti</small>
                </div>
            <?php else:
                $phi3Installed = $ai->isModelDownloaded('phi3');
                ?>
                <div style="margin-top: 30px; max-width: 100%; box-sizing: border-box;">
                    <h3 style="color: var(--yellow-neon); margin-bottom: 15px;">📦 Modello AI</h3>
                    <div
                        style="margin-bottom: 15px; padding: 15px; background: var(--dark-primary); border-radius: 8px; box-sizing: border-box;">
                        <strong>phi3:mini:</strong>
                        <?php if ($phi3Installed): ?><span class="status-badge status-ok">✓ Scaricato</span>
                        <?php else: ?><span class="status-badge status-error">✗ Mancante</span>
                            <button class="install-btn" onclick="downloadModel('phi3:mini')" style="margin-top: 10px;">⬇️
                                Scarica phi3:mini (2.3GB)</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <button onclick="closeSettings()"
                style="width: 100%; padding: 18px; background: linear-gradient(135deg, var(--yellow-neon), var(--brown-light)); color: var(--dark-primary); border: none; border-radius: 12px; margin-top: 40px; cursor: pointer; font-weight: bold; font-size: 1.1rem; box-sizing: border-box;">✓
                Chiudi Impostazioni</button>
        </div>
    </div>

    <h1>🧠 AI Dashboard</h1>
    <div class="menu">
        <a href="Index.php">ISCRITTI</a>
        <a href="corsi.php">CORSI</a>
        <a href="gestione.php">GESTIONE CORSI</a>
        <a href="ai_dashboard.php" style="background: var(--yellow-neon); color: var(--dark-primary);">🧠 AI</a>
    </div>

    <div class="ai-container">
        <div class="chatbot-card">
            <div class="chatbot-header">
                <div class="chatbot-avatar">🤖</div>
                <div>
                    <h2 style="color: var(--yellow-neon); margin: 0;">PalestraBot</h2>
                    <small style="color: #888;">Assistente AI • Conosce tutti i dati della palestra</small>
                </div>
            </div>
            <div class="chatbot-messages" id="chatMessages">
                <div class="chat-message bot">
                    👋 Ciao! Sono <strong>PalestraBot</strong>, il tuo assistente AI.<br><br>
                    Conosco tutti i dati della palestra: iscritti, corsi, prenotazioni e scadenze.<br>
                    Chiedimi qualcosa, ad esempio: "Quanti iscritti ci sono?", "Chi ha l'abbonamento in scadenza?",
                    "Quali corsi ci sono il lunedì?"
                </div>
            </div>
            <div class="typing-indicator" id="typingIndicator"><span class="typing-dots">🤖 Sto
                    pensando<span>.</span><span>.</span><span>.</span></span></div>
            <div class="chat-input-container">
                <input type="text" class="chat-input" id="chatInput" placeholder="Scrivi un messaggio..."
                    onkeypress="if(event.key==='Enter') sendChat()">
                <button class="chat-send" id="chatSendBtn" onclick="sendChat()">Invia 📤</button>
            </div>
        </div>

        <div class="ai-card">
            <h3>✍️ Generatore Descrizioni Corso</h3>
            <form class="ai-form" method="POST">
                <input type="hidden" name="action" value="genera_descrizione">
                <input type="text" name="nome_corso" placeholder="Nome corso (es. Yoga)" required>
                <select name="livello" required>
                    <option value="">Seleziona livello</option>
                    <option value="principiante">Principiante</option>
                    <option value="intermedio">Intermedio</option>
                    <option value="avanzato">Avanzato</option>
                    <option value="esperto">Esperto</option>
                </select>
                <input type="number" name="durata" placeholder="Durata (minuti)" min="15" max="180" required>
                <button type="submit">🧠 Genera con AI</button>
            </form>
            <?php
            if (($_POST['action'] ?? '') === 'genera_descrizione' && isset($_POST['nome_corso'], $_POST['livello'], $_POST['durata'])) {
                $desc = $ai->generaDescrizione($_POST['nome_corso'], $_POST['livello'], $_POST['durata']);
                echo "<div class='ai-result'><strong>📝 Descrizione generata:</strong><br><br><em>" . nl2br(htmlspecialchars($desc)) . "</em></div>";
            }
            ?>
        </div>

        <div class="ai-card">
            <h3>💡 Consigli Corsi per Cliente</h3>
            <form class="ai-form" method="POST">
                <input type="hidden" name="action" value="genera_consigli">
                <select name="id_cliente" required>
                    <option value="">Seleziona cliente</option>
                    <?php
                    $iscritti = $db->GetIscritto();
                    foreach ($iscritti as $iscritto) {
                        echo "<option value='{$iscritto->ID_iscritti}'>{$iscritto->Nome} {$iscritto->Cognome}</option>";
                    }
                    ?>
                </select>
                <button type="submit">🧠 Genera Consigli con AI</button>
            </form>
            <?php
            if (($_POST['action'] ?? '') === 'genera_consigli' && isset($_POST['id_cliente'])) {
                $consigli = $ai->generaConsigli($_POST['id_cliente']);
                echo "<div class='ai-result'><strong>💡 Consigli generati:</strong><br><br><em>" . nl2br(htmlspecialchars($consigli)) . "</em></div>";
            }
            ?>
        </div>

        <div class="ai-card">
            <h3>📊 Report AI Palestra</h3>
            <form class="ai-form" method="POST">
                <input type="hidden" name="action" value="genera_report">
                <button type="submit">🧠 Genera Report con AI</button>
            </form>
            <?php
            if (($_POST['action'] ?? '') === 'genera_report') {
                $report = $ai->generaReport();
                echo "<div class='ai-result'><strong>📊 Report generato:</strong><br><br><em>" . nl2br(htmlspecialchars($report)) . "</em></div>";
            }
            ?>
        </div>
    </div>

    <script>
        function openSettings() { document.getElementById('settingsModal').classList.add('active'); checkInstallationStatus(); }
        function closeSettings() { document.getElementById('settingsModal').classList.remove('active'); }
        function checkInstallationStatus() {
            var btn = document.getElementById('installBtn');
            var stopBtn = document.getElementById('stopBtn');
            var progressContainer = document.getElementById('progressContainer');
            if (!btn || !progressContainer) return;
            fetch('api_ai.php?azione=status').then(function (r) { return r.json(); }).then(function (status) {
                if (status.step !== 'idle' && !status.done) {
                    btn.style.display = 'none';
                    if (stopBtn) stopBtn.style.display = 'block';
                    progressContainer.style.display = 'block';
                    document.getElementById('progressBar').style.width = status.progress + '%';
                    document.getElementById('progressPercent').textContent = status.progress + '%';
                    document.getElementById('progressMessage').textContent = status.message;
                    if (!window.installPolling) startInstallPolling();
                } else if (status.done && status.error) {
                    btn.style.display = 'block';
                    btn.innerHTML = '🔄 Riprova Installazione';
                    if (stopBtn) stopBtn.style.display = 'none';
                    progressContainer.style.display = 'none';
                } else if (status.done && !status.error) { location.reload(); }
                else {
                    btn.style.display = 'block';
                    btn.innerHTML = '🚀 Installa AI (Auto-Setup)';
                    if (stopBtn) stopBtn.style.display = 'none';
                    progressContainer.style.display = 'none';
                }
            });
        }
        function startInstallPolling() {
            var progressBar = document.getElementById('progressBar');
            var progressPercent = document.getElementById('progressPercent');
            var progressMessage = document.getElementById('progressMessage');
            var btn = document.getElementById('installBtn');
            var stopBtn = document.getElementById('stopBtn');
            var progressContainer = document.getElementById('progressContainer');
            window.installPolling = setInterval(function () {
                fetch('api_ai.php?azione=status').then(function (r) { return r.json(); }).then(function (status) {
                    progressBar.style.width = status.progress + '%';
                    progressPercent.textContent = status.progress + '%';
                    progressMessage.textContent = status.message;
                    if (status.error) { progressBar.style.background = 'linear-gradient(90deg, #ff4444, #cc0000)'; progressMessage.style.color = '#ff4444'; }
                    if (status.done) {
                        clearInterval(window.installPolling);
                        window.installPolling = null;
                        if (status.error) {
                            setTimeout(function () { btn.style.display = 'block'; btn.innerHTML = '🔄 Riprova Installazione'; if (stopBtn) stopBtn.style.display = 'none'; progressContainer.style.display = 'none'; }, 3000);
                        } else {
                            if (stopBtn) stopBtn.style.display = 'none';
                            progressMessage.textContent = '✅ Completato! Ricarico la pagina...';
                            setTimeout(function () { location.reload(); }, 2000);
                        }
                    }
                });
            }, 1000);
        }
        function installAI() {
            if (!confirm('Installare Ollama e modello AI?\n\nDownload: ~55MB + Modello 2.3GB\nTempo: 5-10 minuti')) return;
            var btn = document.getElementById('installBtn');
            var stopBtn = document.getElementById('stopBtn');
            var progressContainer = document.getElementById('progressContainer');
            var progressBar = document.getElementById('progressBar');
            var progressPercent = document.getElementById('progressPercent');
            var progressMessage = document.getElementById('progressMessage');
            progressBar.style.width = '0%';
            progressBar.style.background = 'linear-gradient(90deg, #00ff88, #00cc70)';
            progressPercent.textContent = '0%';
            progressMessage.textContent = 'Preparazione...';
            progressMessage.style.color = 'var(--yellow-neon)';
            btn.style.display = 'none';
            stopBtn.style.display = 'block';
            progressContainer.style.display = 'block';
            fetch('api_ai.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'azione=reset' }).finally(function () {
                fetch('api_ai.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'azione=install' }).catch(function () { });
            });
            startInstallPolling();
        }
        function stopInstall() {
            if (!confirm('Sei sicuro di voler fermare l\'installazione?')) return;
            var stopBtn = document.getElementById('stopBtn');
            stopBtn.disabled = true;
            stopBtn.innerHTML = '⏳ Annullamento in corso...';
            if (window.installPolling) clearInterval(window.installPolling);
            fetch('api_ai.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'azione=stop' }).then(function (r) { return r.json(); }).then(function (data) {
                document.getElementById('installBtn').style.display = 'block';
                document.getElementById('installBtn').innerHTML = '🚀 Installa AI (Auto-Setup)';
                stopBtn.style.display = 'none';
                stopBtn.disabled = false;
                stopBtn.innerHTML = '⏹️ Ferma Installazione';
                document.getElementById('progressContainer').style.display = 'none';
                alert('⏹️ Installazione fermata.');
            }).catch(function () { stopBtn.disabled = false; stopBtn.innerHTML = '⏹️ Ferma Installazione'; alert('Errore durante l\'annullamento.'); });
        }
        function downloadModel(modelName) {
            if (!confirm('Scaricare ' + modelName + '?\n\nTempo stimato: 5-10 minuti')) return;
            var btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '⏳ Download in corso...';
            fetch('api_ai.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ azione: 'download', model: modelName }) }).then(function (r) { return r.json(); }).then(function (data) {
                if (data.success) { alert('✅ ' + modelName + ' avviato! Ricarica tra qualche minuto.'); location.reload(); }
                else { alert('❌ Errore: ' + data.message); btn.disabled = false; btn.innerHTML = '⬇️ Scarica ' + modelName; }
            });
        }
        document.getElementById('settingsModal').addEventListener('click', function (e) { if (e.target === this) closeSettings(); });
        function sendChat() {
            var input = document.getElementById('chatInput');
            var messages = document.getElementById('chatMessages');
            var sendBtn = document.getElementById('chatSendBtn');
            var typing = document.getElementById('typingIndicator');
            var domanda = input.value.trim();
            if (!domanda) return;
            messages.innerHTML += '<div class="chat-message user">' + (domanda.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')) + '</div>';
            input.value = '';
            sendBtn.disabled = true;
            typing.classList.add('active');
            messages.scrollTop = messages.scrollHeight;
            fetch('api_ai.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ azione: 'chat', domanda: domanda }) }).then(function (r) { return r.json(); }).then(function (data) {
                typing.classList.remove('active');
                sendBtn.disabled = false;
                var risposta = data.risposta || 'Mi dispiace, non ho capito.';
                risposta = risposta.replace(/\n/g, '<br>');
                messages.innerHTML += '<div class="chat-message bot">' + risposta + '</div>';
                messages.scrollTop = messages.scrollHeight;
            }).catch(function () {
                typing.classList.remove('active');
                sendBtn.disabled = false;
                messages.innerHTML += '<div class="chat-message bot" style="border-left-color: #ff4444;">❌ Errore di connessione. Riprova.</div>';
            });
        }
    </script>
</body>

</html>