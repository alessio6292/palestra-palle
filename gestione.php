<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Palestra Palle - Gestione Corsi</title>
	<link rel="stylesheet" href="style.css">
	<style>
		/* Toast notifiche */
		.toast {
			position: fixed;
			top: 20px;
			right: 20px;
			padding: 15px 25px;
			border-radius: 10px;
			color: white;
			font-weight: bold;
			z-index: 9999;
			animation: slideIn 0.3s ease, fadeOut 0.3s ease 2.7s forwards;
			box-shadow: 0 5px 20px rgba(0,0,0,0.4);
		}
		.toast.success { background: linear-gradient(135deg, #00ff88, #00cc70); color: #111; }
		.toast.error { background: linear-gradient(135deg, #ff4444, #cc0000); }
		@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
		@keyframes fadeOut { to { opacity: 0; transform: translateX(100%); } }
		
		.stato-badge {
			display: inline-block;
			padding: 5px 12px;
			border-radius: 15px;
			font-weight: bold;
			font-size: 0.8rem;
		}
		.stato-confermato { background: #00ff88; color: #111; }
		.stato-attesa { background: #ffa500; color: #111; }
		.stato-ritirato { background: #888; color: white; }
		.stato-completato { background: #4d96ff; color: white; }
		
		/* Badge scadenza */
		.scadenza-badge {
			display: inline-block;
			padding: 5px 12px;
			border-radius: 15px;
			font-weight: bold;
			font-size: 0.8rem;
		}
		.scadenza-ok { background: #00ff88; color: #111; }
		.scadenza-warning { background: #ffd700; color: #111; }
		.scadenza-danger { background: #ff8800; color: #111; }
		.scadenza-critical { 
			background: linear-gradient(135deg, #ff4444, #cc0000);
			color: white;
			animation: pulse 1.5s ease-in-out infinite;
		}
		.scadenza-scaduto {
			background: #666;
			color: white;
			text-decoration: line-through;
		}
		@keyframes pulse {
			0%, 100% { transform: scale(1); opacity: 1; }
			50% { transform: scale(1.05); opacity: 0.9; }
		}
	</style>
</head>
<body>
	<h1>Palestra Palle</h1>

	<!-- Menu di navigazione -->
	<div class="menu">
		<a href="Index.php">ISCRITTI</a>
		<a href="corsi.php">CORSI</a>
		<a href="gestione.php">GESTIONE CORSI</a>
		<a href="ai_dashboard.php">🧠 AI</a>
	</div>

	<?PHP
		require('db.php');
		$messaggio = $_SESSION['messaggio'] ?? null;
		$tipo_messaggio = $_SESSION['tipo_messaggio'] ?? 'success';
		unset($_SESSION['messaggio'], $_SESSION['tipo_messaggio']);
		
		// Funzione per convertire il numero del giorno in nome
		function getNomeGiorno($numero) {
			$giorni = [
				1 => 'Lunedì',
				2 => 'Martedì',
				3 => 'Mercoledì',
				4 => 'Giovedì',
				5 => 'Venerdì',
				6 => 'Sabato',
				7 => 'Domenica'
			];
			return $giorni[$numero] ?? $numero;
		}
		
		// Leggi l'azione dall'URL (lista, nuovo, modifica, cancella, rinnova)
		$azione = $_GET['azione'] ?? 'lista';
		$iscrizioni = [];
		$iscrizione = null;
		
		// ========== RINNOVA ISCRIZIONE ==========
		if($azione == 'rinnova' && isset($_GET['id'])) {
			if($db->RinnovaIscrizione($_GET['id'])) {
				$_SESSION['messaggio'] = '🔄 Iscrizione rinnovata con successo!';
				$_SESSION['tipo_messaggio'] = 'success';
			} else {
				$_SESSION['messaggio'] = '❌ Errore nel rinnovo dell\'iscrizione';
				$_SESSION['tipo_messaggio'] = 'error';
			}
			header('Location: gestione.php');
			exit;
		}
		
		// ========== CANCELLA ==========
		if($azione == 'cancella' && isset($_GET['id'])) {
			$db->CancellaIscrizione_corso($_GET['id']);
			$_SESSION['messaggio'] = '✅ Iscrizione eliminata con successo!';
			$_SESSION['tipo_messaggio'] = 'success';
			header('Location: gestione.php');
			exit;
		}
		
		// ========== MODIFICA/NUOVO - Carica dati ==========
		if($azione == 'modifica' && isset($_GET['id'])) {
			$iscrizione = $db->GetIscrizione($_GET['id']);
		}
		
		// ========== NUOVO - Crea oggetto vuoto ==========
		if($azione == 'nuovo' || ($azione == 'modifica' && !isset($_GET['id']))) {
			if(!$iscrizione) {
				$iscrizione = (object)[
					'ID_iscrizione' => 0,
					'ID_corsi' => '',
					'ID_iscritti' => '',
					'data_iscrizione' => date('Y-m-d'),
					'stato_partecipazione' => 'Confermato',
					'note_particolari' => ''
				];
			}
		}
		
		// ========== SALVA - POST dal form ==========
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$idIscrizione = $_POST['ID_iscrizione'] ?? 0;
			$idIscritto = $_POST['ID_iscritti'];
			$idCorso = $_POST['ID_corsi'];
			
			// Controlla iscrizione duplicata SOLO per nuove iscrizioni (non per modifiche)
			if($idIscrizione == 0) {
				$iscrizioneDuplicata = $db->ControllaIscrizioneDuplicata($idIscritto, $idCorso);
				if($iscrizioneDuplicata) {
					$_SESSION['messaggio'] = "⚠️ {$iscrizioneDuplicata->Nome} {$iscrizioneDuplicata->Cognome} è già iscritto/a al corso '{$iscrizioneDuplicata->nome_corso}' con abbonamento valido fino al " . date('d/m/Y', strtotime($iscrizioneDuplicata->Data_scadenza));
					$_SESSION['tipo_messaggio'] = 'error';
					header('Location: gestione.php?azione=nuovo');
					exit;
				}
			}
			
			$iscrizione = (object)[
				'ID' => $idIscrizione,
				'ID_corsi' => $idCorso,
				'ID_iscritti' => $idIscritto,
				'data_iscrizione' => $_POST['data_iscrizione'],
				'stato_partecipazione' => $_POST['stato_partecipazione'],
				// RIMUOVI LE NOTE PER NUOVE ISCRIZIONI
				'note_particolari' => $idIscrizione > 0 ? $_POST['note_particolari'] : ''
			];
			$db->SalvaIscrizione_corso($iscrizione);
			$_SESSION['messaggio'] = '✅ Iscrizione salvata con successo!';
			$_SESSION['tipo_messaggio'] = 'success';
			header('Location: gestione.php');
			exit;
		}
		
		// ========== LEGGI - Lista iscrizioni ==========
		if($azione == 'lista') {
			$iscrizioni = $db->GetIscrizioniConScadenza();
		}
		
		// Carica elenchi per i select
		$corsi = $db->GetCorsi();
		$iscritti = $db->GetIscritto();
	?>
	
	<!-- Toast notifica -->
	<?php if($messaggio): ?>
		<div class="toast <?= $tipo_messaggio ?>"><?= $messaggio ?></div>
	<?php endif; ?>
	
	<!-- ========== VISUALIZZAZIONE LISTA ========== -->
	<?php if($azione == 'lista'): ?>
	
		<h2>Gestione Iscrizioni ai Corsi</h2>
		
		<table>
		<tr>
			<th>ID Iscrizione</th>
			<th>Iscritto</th>
			<th>Corso</th>
			<th>Data Iscrizione</th>
			<th>Scadenza Abbonamento</th>
			<th>Stato Partecipazione</th>
			<th>Note</th>
			<th>Azioni</th>
		</tr>
		<?php
			// Ciclo per mostrare ogni iscrizione
			foreach($iscrizioni as $i){
		?>
			<tr>
				<td><?=($i->ID_iscrizione ?? '')?></td>
				<td><?=($i->iscritto_nome ?? '') . ' ' . ($i->iscritto_cognome ?? '')?></td>
				<td><?=($i->corso_nome_corso ?? '')?></td>
				<td><?=date('d/m/Y', strtotime($i->data_iscrizione ?? ''))?></td>
				<td>
					<?php
						$dataScadenza = $i->Data_scadenza ?? '';
						$oggi = date('Y-m-d');
						$isScaduto = $dataScadenza < $oggi;
						$giorni = floor((strtotime($dataScadenza) - strtotime($oggi)) / 86400);
						$badgeClass = 'scadenza-ok';
						if($isScaduto) $badgeClass = 'scadenza-scaduto';
						elseif($giorni <= 3) $badgeClass = 'scadenza-critical';
						elseif($giorni <= 5) $badgeClass = 'scadenza-danger';
						elseif($giorni <= 7) $badgeClass = 'scadenza-warning';
					?>
					<span class="scadenza-badge <?= $badgeClass ?>">
						<?php if($isScaduto): ?>
							⛔ Scaduto
						<?php else: ?>
							<?= date('d/m/Y', strtotime($dataScadenza)) ?>
						<?php endif; ?>
					</span>
				</td>
				<td>
					<?php
						$stato = $i->stato_partecipazione ?? '';
						$statoClass = 'stato-confermato';
						if($stato == 'In attesa' || $stato == "Lista d'attesa") $statoClass = 'stato-attesa';
						elseif($stato == 'Ritirato') $statoClass = 'stato-ritirato';
						elseif($stato == 'Completato') $statoClass = 'stato-completato';
					?>
					<span class="stato-badge <?= $statoClass ?>"><?= htmlspecialchars($stato) ?></span>
				</td>
				<td><?=($i->note_particolari ?? '')?></td>
				<td>
					<?php if($isScaduto): ?>
						<a href="gestione.php?azione=rinnova&id=<?=($i->ID_iscrizione ?? '')?>" 
						   style="background: linear-gradient(135deg, #00ff88, #00cc70); color: #111; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-weight: bold;"
						   onclick="return confirm('Rinnovare questa iscrizione?')">
							🔄 Rinnova
						</a>
					<?php else: ?>
						<a href="gestione.php?azione=modifica&id=<?=($i->ID_iscrizione ?? '')?>">Modifica</a>
					<?php endif; ?>
					<a href="gestione.php?azione=cancella&id=<?=($i->ID_iscrizione ?? '')?>" onclick="return confirm('Sei sicuro di voler eliminare questa iscrizione?')">Elimina</a>
				</td>
			</tr>
		<?php
			}
		?>
		</table>
		
		<a href="gestione.php?azione=nuovo" class="btn-nuovo">Nuova Iscrizione</a>
	
	<!-- ========== VISUALIZZAZIONE FORM ========== -->
	<?php elseif($azione == 'nuovo' || $azione == 'modifica'): ?>
	
		<h2><?= $iscrizione->ID_iscrizione ? 'Modifica Iscrizione' : 'Nuova Iscrizione' ?></h2>
		
		<form method="POST">
			<input type="hidden" name="ID_iscrizione" value="<?=$iscrizione->ID_iscrizione?>" />
			
			<p>
				<label>Iscritto:</label>
				<select name="ID_iscritti" required>
					<option value="">Seleziona iscritto</option>
					<?php foreach($iscritti as $s): ?>
						<option value="<?=$s->ID_iscritti?>" 
							<?= (isset($iscrizione->ID_iscritti) && $iscrizione->ID_iscritti == $s->ID_iscritti) ? 'selected' : '' ?>>
							<?=$s->Nome . ' ' . $s->Cognome?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
			
			<p>
				<label>Corso:</label>
				<select name="ID_corsi" required>
					<option value="">Seleziona corso</option>
					<?php foreach($corsi as $c): ?>
						<option value="<?=$c->ID_corsi?>" 
							<?= (isset($iscrizione->ID_corsi) && $iscrizione->ID_corsi == $c->ID_corsi) ? 'selected' : '' ?>>
							<?=$c->nome_corso . ' - ' . getNomeGiorno($c->giorno_settimana) . ' ' . date('H:i', strtotime($c->orario_inizio))?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
			
			<p>
				<label>Data Iscrizione:</label>
				<input type="date" name="data_iscrizione" value="<?=$iscrizione->data_iscrizione?>" required />
			</p>
			
			<p>
				<label>Stato Partecipazione:</label>
				<select name="stato_partecipazione" required>
					<option value="">Seleziona stato</option>
					<option value="Confermato" <?= $iscrizione->stato_partecipazione == 'Confermato' ? 'selected' : '' ?>>Confermato</option>
					<option value="In attesa" <?= $iscrizione->stato_partecipazione == 'In attesa' ? 'selected' : '' ?>>In attesa</option>
					<option value="Ritirato" <?= $iscrizione->stato_partecipazione == 'Ritirato' ? 'selected' : '' ?>>Ritirato</option>
					<option value="Completato" <?= $iscrizione->stato_partecipazione == 'Completato' ? 'selected' : '' ?>>Completato</option>
				</select>
			</p>
			
			<?php if($iscrizione->ID_iscrizione > 0): ?>
			<p>
				<label>Note Particolari:</label>
				<textarea name="note_particolari"><?=$iscrizione->note_particolari?></textarea>
			</p>
			<?php else: ?>
			<input type="hidden" name="note_particolari" value="">
			<?php endif; ?>
			
			<button type="submit">Salva</button>
		</form>
		
		<div class="back">
			<a href="gestione.php">Torna all'elenco</a>
		</div>
	
	<?php endif; ?>
	
	<script>
		setTimeout(() => { document.querySelector('.toast')?.remove(); }, 3000);
	</script>
</body>
</html>
