<!DOCTYPE html>
<html>
<head>
	<title>Palestra Palle - Dettaglio Iscritto</title>
	<link rel="stylesheet" href="style.css">
	<style>
		.profile-card {
			background: var(--dark-secondary);
			border: 2px solid var(--yellow-neon);
			border-radius: 15px;
			padding: 30px;
			margin: 20px 0;
			display: grid;
			grid-template-columns: auto 1fr;
			gap: 30px;
			align-items: start;
		}
		
		.profile-avatar {
			width: 120px;
			height: 120px;
			background: linear-gradient(135deg, var(--yellow-neon), var(--brown-light));
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 3rem;
			color: var(--dark-primary);
			font-weight: bold;
		}
		
		.profile-info h2 {
			color: var(--yellow-neon);
			margin-bottom: 15px;
			font-size: 2rem;
		}
		
		.info-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 15px;
		}
		
		.info-item {
			padding: 10px 15px;
			background: var(--dark-primary);
			border-radius: 8px;
		}
		
		.info-item label {
			color: #888;
			font-size: 0.85rem;
			display: block;
		}
		
		.info-item span {
			color: white;
			font-size: 1.1rem;
			font-weight: bold;
		}
		
		/* Badge scadenza */
		.scadenza-badge {
			display: inline-block;
			padding: 8px 15px;
			border-radius: 20px;
			font-weight: bold;
			font-size: 0.9rem;
		}
		
		.scadenza-ok { background: #00ff88; color: #111; }
		.scadenza-warning { background: #ffa500; color: #111; }
		.scadenza-danger { background: #ff4444; color: white; animation: pulse 1s infinite; }
		.scadenza-critical { background: linear-gradient(45deg, #ff0000, #cc0000); color: white; animation: pulse 0.5s infinite; }
		.scadenza-expired { background: #333; color: #ff4444; text-decoration: line-through; }
		
		@keyframes pulse {
			0%, 100% { opacity: 1; transform: scale(1); }
			50% { opacity: 0.8; transform: scale(1.05); }
		}
		
		/* Sezione corsi */
		.corsi-section {
			margin-top: 30px;
		}
		
		.corsi-section h3 {
			color: var(--yellow-neon);
			margin-bottom: 20px;
			padding-bottom: 10px;
			border-bottom: 2px solid var(--brown-medium);
		}
		
		.corso-card {
			background: var(--dark-secondary);
			border: 2px solid var(--brown-medium);
			border-radius: 10px;
			padding: 20px;
			margin-bottom: 15px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			transition: all 0.3s;
		}
		
		.corso-card:hover {
			border-color: var(--yellow-neon);
			transform: translateX(5px);
		}
		
		.corso-info h4 {
			color: var(--yellow-bright);
			margin-bottom: 8px;
		}
		
		.corso-info p {
			color: #aaa;
			margin: 3px 0;
		}
		
		.corso-stato {
			padding: 8px 15px;
			border-radius: 20px;
			font-weight: bold;
			font-size: 0.85rem;
		}
		
		.stato-confermato { background: #00ff88; color: #111; }
		.stato-attesa { background: #ffa500; color: #111; }
		.stato-ritirato { background: #888; color: white; }
		
		.no-corsi {
			text-align: center;
			padding: 40px;
			color: #888;
			background: var(--dark-secondary);
			border-radius: 10px;
		}
		
		.back-btn {
			display: inline-block;
			padding: 12px 30px;
			background: var(--brown-medium);
			color: white;
			text-decoration: none;
			border-radius: 8px;
			margin-top: 20px;
			transition: all 0.3s;
		}
		
		.back-btn:hover {
			background: var(--yellow-neon);
			color: var(--dark-primary);
		}
	</style>
</head>
<body>
	<h1>Palestra Palle</h1>

	<div class="menu">
		<a href="Index.php">ISCRITTI</a>
		<a href="corsi.php">CORSI</a>
		<a href="gestione.php">GESTIONE CORSI</a>
		<a href="ai_dashboard.php">🧠 AI</a>
	</div>

	<?php
		require('db.php');
		
		// Funzione per convertire il numero del giorno in nome
		function getNomeGiorno($numero) {
			$giorni = [1 => 'Lunedì', 2 => 'Martedì', 3 => 'Mercoledì', 4 => 'Giovedì', 5 => 'Venerdì', 6 => 'Sabato', 7 => 'Domenica'];
			return $giorni[$numero] ?? $numero;
		}
		
		// Funzione per calcolare badge scadenza
		function getScadenzaBadge($dataScadenza) {
			if(empty($dataScadenza)) return ['class' => 'scadenza-expired', 'text' => 'Non iscritto'];
			
			$oggi = new DateTime();
			$scadenza = new DateTime($dataScadenza);
			$diff = $oggi->diff($scadenza);
			$giorni = $diff->invert ? -$diff->days : $diff->days;
			
			if($giorni < 0) {
				return ['class' => 'scadenza-expired', 'text' => 'Scaduto da ' . abs($giorni) . ' giorni'];
			} elseif($giorni <= 3) {
				return ['class' => 'scadenza-critical', 'text' => '⚠️ SCADE TRA ' . $giorni . ' GIORNI!'];
			} elseif($giorni <= 7) {
				return ['class' => 'scadenza-danger', 'text' => '⚠️ Scade tra ' . $giorni . ' giorni'];
			} elseif($giorni <= 15) {
				return ['class' => 'scadenza-warning', 'text' => 'Scade tra ' . $giorni . ' giorni'];
			} else {
				return ['class' => 'scadenza-ok', 'text' => 'Valido fino al ' . $scadenza->format('d/m/Y')];
			}
		}
		
		$id = $_GET['id'] ?? 0;
		if(!$id) {
			header('Location: Index.php');
			exit;
		}
		
		$iscritto = $db->GetIscrittoId($id);
		if(!$iscritto || !isset($iscritto->ID_iscritti)) {
			echo "<p style='color: #ff4444; text-align: center; padding: 50px;'>Iscritto non trovato</p>";
			echo "<a href='Index.php' class='back-btn'>← Torna all'elenco</a>";
			exit;
		}
		
		// Ottieni corsi dell'iscritto
		$corsiIscritto = $db->GetCorsiByIscritto($id);
		$scadenzaInfo = getScadenzaBadge($iscritto->Data_scadenza ?? '');
	?>
	
	<div class="profile-card">
		<div class="profile-avatar">
			<?= strtoupper(substr($iscritto->Nome ?? '', 0, 1) . substr($iscritto->Cognome ?? '', 0, 1)) ?>
		</div>
		<div class="profile-info">
			<h2><?= htmlspecialchars(($iscritto->Nome ?? '') . ' ' . ($iscritto->Cognome ?? '')) ?></h2>
			
			<div class="info-grid">
				<div class="info-item">
					<label>📧 Email</label>
					<span><?= htmlspecialchars($iscritto->Email ?? '') ?></span>
				</div>
				<div class="info-item">
					<label>📱 Telefono</label>
					<span><?= htmlspecialchars($iscritto->Telefono ?? '') ?></span>
				</div>
				<div class="info-item">
					<label>🎂 Data di Nascita</label>
					<span><?= date('d/m/Y', strtotime($iscritto->Data_nascita ?? '')) ?></span>
				</div>
				<div class="info-item">
					<label>💳 Abbonamento</label>
					<span><?= htmlspecialchars($iscritto->Tipo_abbonamento ?? 'Nessuno') ?></span>
				</div>
				<div class="info-item">
					<label>📅 Scadenza</label>
					<span class="scadenza-badge <?= $scadenzaInfo['class'] ?>"><?= $scadenzaInfo['text'] ?></span>
				</div>
				<div class="info-item">
					<label>📊 Stato</label>
					<span><?= htmlspecialchars($iscritto->Stato ?? '') ?></span>
				</div>
			</div>
		</div>
	</div>
	
	<div class="corsi-section">
		<h3>📚 Corsi Iscritto (<?= count($corsiIscritto) ?>)</h3>
		
		<?php if(count($corsiIscritto) > 0): ?>
			<?php foreach($corsiIscritto as $ci): ?>
				<div class="corso-card">
					<div class="corso-info">
						<h4><?= htmlspecialchars($ci->nome_corso ?? '') ?></h4>
						<p>🧑‍🏫 Istruttore: <?= htmlspecialchars($ci->istruttore ?? '') ?></p>
						<p>📅 <?= getNomeGiorno($ci->giorno_settimana ?? '') ?> • 🕐 <?= date('H:i', strtotime($ci->orario_inizio ?? '')) ?> - <?= date('H:i', strtotime($ci->orario_fine ?? '')) ?></p>
						<p>📊 Livello: <?= htmlspecialchars($ci->livello ?? '') ?></p>
						<p style="color: #666; font-size: 0.85rem;">Iscritto dal: <?= date('d/m/Y', strtotime($ci->data_iscrizione ?? '')) ?></p>
					</div>
					<?php 
						$statoClass = 'stato-confermato';
						if(($ci->stato_partecipazione ?? '') == 'In attesa' || ($ci->stato_partecipazione ?? '') == "Lista d'attesa") $statoClass = 'stato-attesa';
						if(($ci->stato_partecipazione ?? '') == 'Ritirato') $statoClass = 'stato-ritirato';
					?>
					<span class="corso-stato <?= $statoClass ?>"><?= htmlspecialchars($ci->stato_partecipazione ?? '') ?></span>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="no-corsi">
				<p style="font-size: 3rem; margin-bottom: 15px;">📭</p>
				<p>Questo iscritto non è ancora iscritto a nessun corso.</p>
				<a href="gestione.php?azione=nuovo" class="back-btn" style="margin-top: 15px;">+ Iscrivilo a un corso</a>
			</div>
		<?php endif; ?>
	</div>
	
	<a href="Index.php" class="back-btn">← Torna all'elenco iscritti</a>
	
</body>
</html>
