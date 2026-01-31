<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Palestra Palle - Iscritti</title>
	<link rel="stylesheet" href="style.css">
	<style>
		.scadenza-badge { display: inline-block; padding: 5px 12px; border-radius: 15px; font-weight: bold; font-size: 0.8rem; white-space: nowrap; }
		.scadenza-ok { background: #00ff88; color: #111; }
		.scadenza-warning { background: #ffa500; color: #111; }
		.scadenza-danger { background: #ff4444; color: white; animation: pulse 1s infinite; }
		.scadenza-critical { background: linear-gradient(45deg, #ff0000, #990000); color: white; animation: pulse 0.5s infinite; }
		.scadenza-expired { background: #444; color: #ff4444; }
		@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
		tr.clickable-row { cursor: pointer; }
		.toast { position: fixed; top: 20px; right: 20px; padding: 15px 25px; border-radius: 10px; color: white; font-weight: bold; z-index: 9999; animation: slideIn 0.3s ease, fadeOut 0.3s ease 2.7s forwards; box-shadow: 0 5px 20px rgba(0,0,0,0.4); }
		.toast.success { background: linear-gradient(135deg, #00ff88, #00cc70); color: #111; }
		.toast.error { background: linear-gradient(135deg, #ff4444, #cc0000); }
		.toast.warning { background: linear-gradient(135deg, #ffa500, #ff8c00); color: #111; }
		@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
		@keyframes fadeOut { to { opacity: 0; transform: translateX(100%); } }
		.view-link { color: var(--yellow-neon); text-decoration: none; font-weight: bold; }
		.view-link:hover { text-decoration: underline; }
		.error-msg { color: #ff4444; font-size: 0.85rem; }
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

	<?PHP
		require('db.php');
		$messaggio = $_SESSION['messaggio'] ?? null;
		$tipo_messaggio = $_SESSION['tipo_messaggio'] ?? 'success';
		unset($_SESSION['messaggio'], $_SESSION['tipo_messaggio']);
		
		function getScadenzaBadge($dataScadenza) {
			if(empty($dataScadenza)) return ['class' => 'scadenza-expired', 'text' => 'N/A', 'giorni' => -999];
			$oggi = new DateTime();
			$scadenza = new DateTime($dataScadenza);
			$diff = $oggi->diff($scadenza);
			$giorni = $diff->invert ? -$diff->days : $diff->days;
			if($giorni < 0) return ['class' => 'scadenza-expired', 'text' => 'Scaduto', 'giorni' => $giorni];
			if($giorni <= 3) return ['class' => 'scadenza-critical', 'text' => '⚠️ ' . $giorni . 'gg!', 'giorni' => $giorni];
			if($giorni <= 7) return ['class' => 'scadenza-danger', 'text' => $giorni . ' giorni', 'giorni' => $giorni];
			if($giorni <= 15) return ['class' => 'scadenza-warning', 'text' => $giorni . ' giorni', 'giorni' => $giorni];
			return ['class' => 'scadenza-ok', 'text' => date('d/m/Y', strtotime($dataScadenza)), 'giorni' => $giorni];
		}
		
		$azione = $_GET['azione'] ?? 'lista';
		$iscritti = [];
		$iscritto = null;
		
		if($azione == 'cancella' && isset($_GET['id'])) {
			$db->CancellaIscritto($_GET['id']);
			$_SESSION['messaggio'] = '✅ Iscritto eliminato con successo!';
			$_SESSION['tipo_messaggio'] = 'success';
			header('Location: Index.php');
			exit;
		}
		
		if($azione == 'modifica' && isset($_GET['id'])) {
			$iscritto = $db->GetIscrittoId($_GET['id']);
		}
		
		if($azione == 'nuovo' || ($azione == 'modifica' && !isset($_GET['id']))) {
			if(!$iscritto) {
				$iscritto = (object)['ID_iscritti' => 0, 'Nome' => '', 'Cognome' => '', 'Data_nascita' => '', 'Email' => '', 'Telefono' => '', 'Tipo_abbonamento' => '', 'Data_scadenza' => '', 'Stato' => ''];
			}
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$iscritto = (object)[
				'ID' => $_POST['ID_iscritti'] ?? 0,
				'Nome' => $_POST['Nome'],
				'Cognome' => $_POST['Cognome'],
				'Data_nascita' => $_POST['Data_nascita'],
				'Email' => $_POST['Email'],
				'Telefono' => $_POST['Telefono'],
				'Tipo_abbonamento' => $_POST['Tipo_abbonamento'],
				'Data_scadenza' => $_POST['Data_scadenza'],
				'Stato' => $_POST['Stato']
			];
			if (strlen($_POST['Telefono']) !== 10 || !ctype_digit($_POST['Telefono'])) {
				$_SESSION['messaggio'] = 'Il numero di telefono deve contenere esattamente 10 cifre.';
				$_SESSION['tipo_messaggio'] = 'error';
			} else {
				if (empty($_POST['Data_scadenza'])) {
					$iscritto->Stato = "Non iscritto";
				} else {
					$data_oggi = new DateTime();
					$data_scadenza = new DateTime($_POST['Data_scadenza']);
					$data_oggi->setTime(0, 0, 0);
					$data_scadenza->setTime(0, 0, 0);
					if ($data_scadenza > $data_oggi) $iscritto->Stato = "Attivo";
					elseif ($data_scadenza < $data_oggi) $iscritto->Stato = "Scaduto";
					else $iscritto->Stato = "Scade Oggi";
				}
				if($iscritto->Tipo_abbonamento == "mensile") $interval = new DateInterval('P1M');
				elseif($iscritto->Tipo_abbonamento == "trimestrale") $interval = new DateInterval('P3M');
				elseif($iscritto->Tipo_abbonamento == "semestrale") $interval = new DateInterval('P6M');
				elseif($iscritto->Tipo_abbonamento == "annuale") $interval = new DateInterval('P1Y');
				else $interval = null;
				if($interval){
					$data_attuale = new DateTime();
					$data_scadenza = clone $data_attuale;
					$data_scadenza->add($interval);
					$iscritto->Data_scadenza = $data_scadenza->format('Y-m-d');
				} else {
					$iscritto->Data_scadenza = '';
				}
				$db->SalvaIscritto($iscritto);
				$_SESSION['messaggio'] = '✅ Iscritto salvato con successo!';
				$_SESSION['tipo_messaggio'] = 'success';
				header('Location: Index.php');
				exit;
			}
		}
		
		if($azione == 'lista') {
			$iscritti = $db->GetIscritto();
		}
	?>
	
	<?php if($messaggio): ?>
		<div class="toast <?= $tipo_messaggio ?>"><?= htmlspecialchars($messaggio) ?></div>
	<?php endif; ?>
	
	<?php if($azione == 'lista'): ?>
		<h2>Elenco Iscritti</h2>
		<table>
		<tr>
			<th>ID</th><th>Nome</th><th>Cognome</th><th>Data</th><th>Email</th><th>Telefono</th><th>Tipo Abbonamento</th><th>Scadenza</th><th>Stato</th><th>Azioni</th>
		</tr>
		<?php foreach($iscritti as $s){
			$scadenzaInfo = getScadenzaBadge($s->Data_scadenza ?? '');
		?>
			<tr class="clickable-row" onclick="window.location='dettaglio_iscritto.php?id=<?= (int)($s->ID_iscritti ?? 0) ?>'">
				<td><?= (int)($s->ID_iscritti ?? 0) ?></td>
				<td><a href="dettaglio_iscritto.php?id=<?= (int)($s->ID_iscritti ?? 0) ?>" class="view-link"><?= htmlspecialchars($s->Nome ?? '') ?></a></td>
				<td><?= htmlspecialchars($s->Cognome ?? '') ?></td>
				<td><?= date('d/m/Y', strtotime($s->Data_nascita ?? '')) ?></td>
				<td><?= htmlspecialchars($s->Email ?? '') ?></td>
				<td><?= htmlspecialchars($s->Telefono ?? '') ?></td>
				<td><?= htmlspecialchars($s->Tipo_abbonamento ?? '') ?></td>
				<td><span class="scadenza-badge <?= $scadenzaInfo['class'] ?>"><?= $scadenzaInfo['text'] ?></span></td>
				<td><?= htmlspecialchars($s->Stato ?? '') ?></td>
				<td onclick="event.stopPropagation();">
					<a href="Index.php?azione=modifica&id=<?= (int)($s->ID_iscritti ?? 0) ?>">Modifica</a>
					<a href="Index.php?azione=cancella&id=<?= (int)($s->ID_iscritti ?? 0) ?>" onclick="return confirm('Sei sicuro di voler eliminare questo iscritto?')">Elimina</a>
				</td>
			</tr>
		<?php } ?>
		</table>
		<a href="Index.php?azione=nuovo" class="btn-nuovo">Nuovo Iscritto</a>
	
	<?php elseif($azione == 'nuovo' || $azione == 'modifica'): ?>
		<h2><?= ($iscritto->ID_iscritti ?? 0) ? 'Modifica Iscritto' : 'Nuovo Iscritto' ?></h2>
		<form method="POST">
			<input type="hidden" name="ID_iscritti" value="<?= (int)($iscritto->ID_iscritti ?? 0) ?>" />
			<p><label>Nome:</label> <input type="text" name="Nome" value="<?= htmlspecialchars($iscritto->Nome ?? '') ?>" required pattern="[A-Za-zÀ-ÿ\s]{2,}" /></p>
			<p><label>Cognome:</label> <input type="text" name="Cognome" value="<?= htmlspecialchars($iscritto->Cognome ?? '') ?>" required pattern="[A-Za-zÀ-ÿ\s]{2,}" /></p>
			<p><label>Data Nascita:</label> <input type="date" name="Data_nascita" value="<?= htmlspecialchars($iscritto->Data_nascita ?? '') ?>" min="1920-01-01" max="2018-12-31" required /></p>
			<p><label>Email:</label> <input type="email" name="Email" value="<?= htmlspecialchars($iscritto->Email ?? '') ?>" required /></p>
			<p><label>Telefono:</label> <input type="tel" name="Telefono" value="<?= htmlspecialchars($iscritto->Telefono ?? '') ?>" pattern="\d{10}" maxlength="10" minlength="10" required title="10 cifre" /></p>
			<p><label>Tipo Abbonamento:</label>
				<select name="Tipo_abbonamento" required>
					<option value="">Seleziona</option>
					<option value="mensile" <?= ($iscritto->Tipo_abbonamento ?? '') == 'mensile' ? 'selected' : '' ?>>mensile</option>
					<option value="trimestrale" <?= ($iscritto->Tipo_abbonamento ?? '') == 'trimestrale' ? 'selected' : '' ?>>trimestrale</option>
					<option value="semestrale" <?= ($iscritto->Tipo_abbonamento ?? '') == 'semestrale' ? 'selected' : '' ?>>semestrale</option>
					<option value="annuale" <?= ($iscritto->Tipo_abbonamento ?? '') == 'annuale' ? 'selected' : '' ?>>annuale</option>
				</select>
			</p>
			<p><label>Data Scadenza:</label> <input type="date" name="Data_scadenza" value="<?= htmlspecialchars($iscritto->Data_scadenza ?? '') ?>" readonly /></p>
			<p><label>Stato:</label> <input type="text" name="Stato" value="<?= htmlspecialchars($iscritto->Stato ?? '') ?>" readonly /></p>
			<button type="submit">Salva</button>
		</form>
		<div class="back"><a href="Index.php">Torna all'elenco</a></div>
	<?php endif; ?>
	
	<script>setTimeout(function(){ var t=document.querySelector('.toast'); if(t) t.remove(); }, 3000);</script>
</body>
</html>
