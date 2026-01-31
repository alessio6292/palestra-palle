<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
	<title>Palestra Palle - Corsi</title>
	<link rel="stylesheet" href="style.css">
	<style>
		/* ===== SEARCH & FILTER BAR ===== */
		.search-container {
			width: 95%;
			max-width: 1200px;
			margin: 30px auto 20px;
			display: flex;
			flex-direction: column;
			gap: 15px;
		}

		.search-bar {
			display: flex;
			align-items: center;
			background: var(--dark-secondary);
			padding: 15px;
			border-radius: 15px;
			box-shadow: var(--shadow-medium);
			border: 1px solid rgba(255, 215, 0, 0.2);
			flex-wrap: wrap;
			/* Allow wrapping on small screens */
			gap: 15px;
		}

		.search-bar:focus-within {
			border-color: var(--yellow-neon);
			box-shadow: 0 0 20px rgba(255, 215, 0, 0.15);
		}

		.search-icon {
			font-size: 1.2rem;
			color: var(--yellow-neon);
			padding: 0 15px;
		}

		.search-bar input[type="text"] {
			flex: 1;
			min-width: 250px;
			background: var(--dark-primary);
			border: 2px solid var(--brown-medium);
			border-radius: 8px;
			color: white;
			font-size: 1rem;
			height: 50px;
			padding: 0 15px;
			box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.3);
		}

		.search-bar input[type="text"]:focus {
			border-color: var(--yellow-neon);
			box-shadow: 0 0 15px rgba(255, 215, 0, 0.1);
			outline: none;
		}

		.search-actions {
			display: flex;
			gap: 15px;
			align-items: center;
		}

		.filter-btn,
		.search-btn {
			height: 50px;
			/* Same height for both */
			padding: 0 30px;
			border-radius: 8px;
			font-weight: 700;
			text-transform: uppercase;
			cursor: pointer;
			text-decoration: none;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 10px;
			transition: all 0.3s ease;
			font-size: 1rem;
			box-sizing: border-box;
			/* Ensure padding doesn't add to height */
			line-height: 1;
			/* Normalize line height */
			margin: 0;
			/* Reset margins */
		}

		.filter-btn {
			background: transparent;
			color: var(--yellow-neon);
			border: 2px solid var(--brown-medium);
		}

		.filter-btn:hover,
		.filter-btn.active {
			border-color: var(--yellow-neon);
			background: rgba(255, 215, 0, 0.1);
			transform: translateY(-2px);
		}

		.search-btn {
			background: linear-gradient(135deg, var(--yellow-neon), var(--yellow-bright));
			color: var(--dark-primary);
			border: 2px solid transparent;
			/* Match filter button border width */
			box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
			background-clip: padding-box;
			/* Prevent background bleeding */
		}

		.search-btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(255, 215, 0, 0.5);
			filter: brightness(1.1);
		}

		/* ===== FILTERS PANEL ===== */
		.filters-panel {
			background: var(--dark-secondary);
			padding: 25px;
			border-radius: 15px;
			border: 1px solid rgba(255, 215, 0, 0.2);
			display: none;
			animation: slideDown 0.3s ease-out;
		}

		.filters-panel.show {
			display: block;
		}

		@keyframes slideDown {
			from {
				opacity: 0;
				transform: translateY(-10px);
			}

			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.filters-row {
			display: flex;
			gap: 20px;
			align-items: flex-end;
			flex-wrap: wrap;
		}

		.filter-group {
			flex: 1;
			min-width: 200px;
		}

		.filter-group label {
			font-size: 0.9rem;
			margin-bottom: 8px;
			color: var(--yellow-neon);
			font-weight: bold;
			text-transform: uppercase;
		}

		.filter-group select {
			height: 50px;
			background: var(--dark-primary);
			border: 2px solid var(--brown-medium);
			border-radius: 8px;
			padding: 0 15px;
			color: white;
			width: 100%;
			font-size: 1rem;
		}

		.filter-group select:focus {
			border-color: var(--yellow-neon);
			outline: none;
		}

		.filter-actions-row {
			display: flex;
			gap: 15px;
		}

		.btn-apply,
		.btn-reset {
			height: 50px;
			padding: 0 30px;
			border-radius: 8px;
			font-weight: 700;
			text-transform: uppercase;
			cursor: pointer;
			text-decoration: none;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 10px;
			font-size: 1rem;
			box-sizing: border-box;
			line-height: 1;
			margin: 0;
			transition: all 0.3s ease;
		}

		.btn-apply {
			background: linear-gradient(135deg, var(--yellow-neon), var(--brown-light));
			color: var(--dark-primary);
			border: 2px solid transparent;
			/* Match reset button border width */
			background-clip: padding-box;
		}

		.btn-apply:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
		}

		.btn-reset {
			background: transparent;
			border: 2px solid var(--brown-medium);
			color: var(--white-soft);
		}

		.btn-reset:hover {
			border-color: #ff4444;
			color: #ff4444;
			transform: translateY(-2px);
		}

		/* Toast animation */
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
		}

		.toast.success {
			background: linear-gradient(135deg, #00ff88, #00cc70);
			color: #111;
		}

		.toast.error {
			background: linear-gradient(135deg, #ff4444, #cc0000);
		}

		@keyframes slideIn {
			from {
				transform: translateX(100%);
				opacity: 0;
			}

			to {
				transform: translateX(0);
				opacity: 1;
			}
		}

		@keyframes fadeOut {
			to {
				opacity: 0;
				transform: translateX(100%);
			}
		}

		th a {
			color: inherit;
			text-decoration: none;
		}

		th a:hover {
			color: var(--yellow-neon);
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

	<?PHP
	require('db.php');
	function getNomeGiorno($n)
	{
		$g = [1 => 'Lunedì', 2 => 'Martedì', 3 => 'Mercoledì', 4 => 'Giovedì', 5 => 'Venerdì', 6 => 'Sabato', 7 => 'Domenica'];
		return $g[$n] ?? $n;
	}
	$messaggio = $_SESSION['messaggio'] ?? null;
	$tipo_messaggio = $_SESSION['tipo_messaggio'] ?? 'success';
	unset($_SESSION['messaggio'], $_SESSION['tipo_messaggio']);
	$azione = $_GET['azione'] ?? 'lista';
	$corsi = [];
	$corso = null;
	$ricerca = $_GET['q'] ?? '';
	$filtro_giorno = $_GET['giorno'] ?? '';
	$filtro_livello = $_GET['livello'] ?? '';
	$ordine = $_GET['ordine'] ?? 'nome_corso';
	$direzione = $_GET['dir'] ?? 'ASC';
	$mostra_filtri = isset($_GET['filtri']) || $filtro_giorno !== '' || $filtro_livello !== '';

	function sortLink($campo, $ordine, $direzione, $ricerca, $filtro_giorno, $filtro_livello)
	{
		$dir = ($ordine === $campo && $direzione === 'ASC') ? 'DESC' : 'ASC';
		$url = 'corsi.php?ordine=' . urlencode($campo) . '&dir=' . $dir;
		if ($ricerca !== '')
			$url .= '&q=' . urlencode($ricerca);
		if ($filtro_giorno !== '')
			$url .= '&giorno=' . urlencode($filtro_giorno);
		if ($filtro_livello !== '')
			$url .= '&livello=' . urlencode($filtro_livello);
		return $url;
	}

	if ($azione == 'cancella' && isset($_GET['id'])) {
		$db->CancellaCorso($_GET['id']);
		$_SESSION['messaggio'] = '✅ Corso eliminato con successo!';
		$_SESSION['tipo_messaggio'] = 'success';
		header('Location: corsi.php');
		exit;
	}
	if ($azione == 'modifica' && isset($_GET['id']))
		$corso = $db->GetCorso($_GET['id']);
	if ($azione == 'nuovo' || ($azione == 'modifica' && !isset($_GET['id']))) {
		if (!$corso)
			$corso = (object) ['ID_corsi' => 0, 'nome_corso' => '', 'istruttore' => '', 'giorno_settimana' => '', 'orario_inizio' => '', 'orario_fine' => '', 'durata' => '', 'posti_disponibili' => '', 'posti_occupati' => 0, 'livello' => ''];
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$errori = [];
		if (empty(trim($_POST['nome_corso'] ?? '')))
			$errori[] = 'Nome corso obbligatorio';
		elseif (strlen(trim($_POST['nome_corso'])) < 2)
			$errori[] = 'Nome corso troppo corto';
		if (empty(trim($_POST['istruttore'] ?? '')))
			$errori[] = 'Istruttore obbligatorio';
		elseif (strlen(trim($_POST['istruttore'])) < 2)
			$errori[] = 'Nome istruttore troppo corto';
		if (empty($_POST['giorno_settimana']))
			$errori[] = 'Giorno obbligatorio';
		$posti = (int) ($_POST['posti_disponibili'] ?? 0);
		if ($posti < 1 || $posti > 100)
			$errori[] = 'Posti devono essere tra 1 e 100';

		if (count($errori) > 0) {
			$_SESSION['messaggio'] = '❌ ' . implode(', ', $errori);
			$_SESSION['tipo_messaggio'] = 'error';
		} else {
			$orario_inizio = '2026-01-01 ' . $_POST['orario_inizio'] . ':00';
			$orario_fine = '2026-01-01 ' . $_POST['orario_fine'] . ':00';
			$durata = 0;
			if (!empty($_POST['orario_inizio']) && !empty($_POST['orario_fine'])) {
				$t1 = strtotime('1970-01-01 ' . $_POST['orario_inizio']);
				$t2 = strtotime('1970-01-01 ' . $_POST['orario_fine']);
				$durata = max(0, (int) round(($t2 - $t1) / 60));
			}
			$corso = (object) [
				'ID' => $_POST['ID_corsi'] ?? 0,
				'nome_corso' => trim($_POST['nome_corso']),
				'istruttore' => trim($_POST['istruttore']),
				'giorno_settimana' => $_POST['giorno_settimana'],
				'orario_inizio' => $orario_inizio,
				'orario_fine' => $orario_fine,
				'durata' => $durata,
				'posti_disponibili' => $_POST['posti_disponibili'],
				'posti_occupati' => $_POST['posti_occupati'] ?? 0,
				'livello' => $_POST['livello']
			];
			$db->SalvaCorso($corso);
			$_SESSION['messaggio'] = '✅ Corso salvato con successo!';
			$_SESSION['tipo_messaggio'] = 'success';
			header('Location: corsi.php');
			exit;
		}
	}

	if ($azione == 'lista') {
		$corsi = $db->GetCorsi();
		if ($ricerca !== '') {
			$r = strtolower($ricerca);
			$corsi = array_filter($corsi, function ($c) use ($r) {
				return strpos(strtolower($c->nome_corso ?? ''), $r) !== false || strpos(strtolower($c->istruttore ?? ''), $r) !== false;
			});
		}
		if ($filtro_giorno !== '')
			$corsi = array_filter($corsi, fn($c) => ($c->giorno_settimana ?? '') == $filtro_giorno);
		if ($filtro_livello !== '')
			$corsi = array_filter($corsi, fn($c) => strtolower($c->livello ?? '') == strtolower($filtro_livello));
		usort($corsi, function ($a, $b) use ($ordine, $direzione) {
			$valA = $a->$ordine ?? '';
			$valB = $b->$ordine ?? '';
			$cmp = is_numeric($valA) ? ($valA - $valB) : strcmp($valA, $valB);
			return $direzione === 'DESC' ? -$cmp : $cmp;
		});
	}
	?>

	<?php if ($messaggio): ?>
		<div class="toast <?= $tipo_messaggio ?>"><?= htmlspecialchars($messaggio) ?></div><?php endif; ?>

	<?php if ($azione == 'lista'): ?>
		<h2>Elenco Corsi</h2>

		<div class="search-container">
			<form method="GET" class="search-bar">
				<input type="text" name="q" placeholder="🔍 Cerca per nome corso o istruttore..."
					value="<?= htmlspecialchars($ricerca) ?>">

				<div class="search-actions">
					<a href="corsi.php?<?= $mostra_filtri ? '' : 'filtri=1&' ?>q=<?= urlencode($ricerca) ?>&giorno=<?= urlencode($filtro_giorno) ?>&livello=<?= urlencode($filtro_livello) ?>"
						class="filter-btn <?= $mostra_filtri ? 'active' : '' ?>">
						<?= $mostra_filtri ? '✕ Nascondi' : '🎛️ Filtri' ?>
					</a>
					<button type="submit" class="search-btn">
						🔍 Cerca
					</button>
				</div>
			</form>

			<div class="filters-panel <?= $mostra_filtri ? 'show' : '' ?>">
				<form method="GET">
					<input type="hidden" name="q" value="<?= htmlspecialchars($ricerca) ?>">
					<div class="filters-row">
						<div class="filter-group">
							<label>📅 Giorno</label>
							<select name="giorno">
								<option value="">Tutti i giorni</option>
								<?php for ($i = 1; $i <= 7; $i++): ?>
									<option value="<?= $i ?>" <?= $filtro_giorno == (string) $i ? 'selected' : '' ?>>
										<?= getNomeGiorno($i) ?>
									</option><?php endfor; ?>
							</select>
						</div>
						<div class="filter-group">
							<label>📊 Livello</label>
							<select name="livello">
								<option value="">Tutti i livelli</option>
								<option value="Principiante" <?= $filtro_livello == 'Principiante' ? 'selected' : '' ?>>
									Principiante</option>
								<option value="Intermedio" <?= $filtro_livello == 'Intermedio' ? 'selected' : '' ?>>Intermedio
								</option>
								<option value="Avanzato" <?= $filtro_livello == 'Avanzato' ? 'selected' : '' ?>>Avanzato
								</option>
								<option value="Esperto" <?= $filtro_livello == 'Esperto' ? 'selected' : '' ?>>Esperto</option>
							</select>
						</div>
						<div class="filter-actions-row">
							<button type="submit" class="btn-apply">✓ Applica</button>
							<a href="corsi.php" class="btn-reset">✕ Reset</a>
						</div>
					</div>
				</form>
			</div>
		</div>

		<table>
			<tr>
				<th><a href="<?= sortLink('ID_corsi', $ordine, $direzione, $ricerca, $filtro_giorno, $filtro_livello) ?>">ID
						⇅</a></th>
				<th><a href="<?= sortLink('nome_corso', $ordine, $direzione, $ricerca, $filtro_giorno, $filtro_livello) ?>">Nome
						Corso ⇅</a></th>
				<th><a href="<?= sortLink('istruttore', $ordine, $direzione, $ricerca, $filtro_giorno, $filtro_livello) ?>">Istruttore
						⇅</a></th>
				<th><a
						href="<?= sortLink('giorno_settimana', $ordine, $direzione, $ricerca, $filtro_giorno, $filtro_livello) ?>">Giorno
						⇅</a></th>
				<th>Ora Inizio</th>
				<th>Ora Fine</th>
				<th>Durata</th>
				<th>Posti Totali</th>
				<th>Occupati</th>
				<th>Disponibili</th>
				<th><a href="<?= sortLink('livello', $ordine, $direzione, $ricerca, $filtro_giorno, $filtro_livello) ?>">Livello
						⇅</a></th>
				<th>Azioni</th>
			</tr>
			<?php foreach ($corsi as $c):
				$disponibili = ($c->posti_disponibili ?? 0) - ($c->posti_occupati ?? 0);
				?>
				<tr>
					<td><?= (int) ($c->ID_corsi ?? 0) ?></td>
					<td><?= htmlspecialchars($c->nome_corso ?? '') ?></td>
					<td><?= htmlspecialchars($c->istruttore ?? '') ?></td>
					<td><?= getNomeGiorno($c->giorno_settimana ?? '') ?></td>
					<td><?= date('H:i', strtotime($c->orario_inizio ?? '')) ?></td>
					<td><?= date('H:i', strtotime($c->orario_fine ?? '')) ?></td>
					<td><?= (int) ($c->durata ?? 0) ?></td>
					<td><?= (int) ($c->posti_disponibili ?? 0) ?></td>
					<td><?= (int) ($c->posti_occupati ?? 0) ?></td>
					<td
						style="font-weight: bold; color: <?= $disponibili == 0 ? '#ff4444' : ($disponibili <= 3 ? '#ffa500' : '#00ff88') ?>">
						<?= $disponibili ?>
					</td>
					<td><?= htmlspecialchars($c->livello ?? '') ?></td>
					<td>
						<a href="corsi.php?azione=modifica&id=<?= (int) ($c->ID_corsi ?? 0) ?>">Modifica</a>
						<a href="corsi.php?azione=cancella&id=<?= (int) ($c->ID_corsi ?? 0) ?>"
							onclick="return confirm('Sei sicuro di voler eliminare questo corso?')">Elimina</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php if (count($corsi) == 0): ?>
			<p style="text-align: center; padding: 30px; color: #888;">Nessun corso trovato.</p><?php endif; ?>
		<a href="corsi.php?azione=nuovo" class="btn-nuovo">Nuovo Corso</a>

	<?php elseif ($azione == 'nuovo' || $azione == 'modifica'): ?>
		<h2><?= ($corso->ID_corsi ?? 0) ? 'Modifica Corso' : 'Nuovo Corso' ?></h2>
		<form method="POST">
			<input type="hidden" name="ID_corsi" value="<?= (int) ($corso->ID_corsi ?? 0) ?>" />
			<p><label>Nome Corso:</label> <input type="text" name="nome_corso"
					value="<?= htmlspecialchars($corso->nome_corso ?? '') ?>" required /></p>
			<p><label>Istruttore:</label> <input type="text" name="istruttore"
					value="<?= htmlspecialchars($corso->istruttore ?? '') ?>" required /></p>
			<p><label>Giorno Settimana:</label>
				<select name="giorno_settimana" required>
					<option value="">Seleziona giorno</option>
					<?php for ($i = 1; $i <= 7; $i++): ?>
						<option value="<?= $i ?>" <?= ($corso->giorno_settimana ?? '') == (string) $i ? 'selected' : '' ?>>
							<?= getNomeGiorno($i) ?>
						</option><?php endfor; ?>
				</select>
			</p>
			<p><label>Ora Inizio:</label> <input type="time" name="orario_inizio"
					value="<?= date('H:i', strtotime($corso->orario_inizio ?? '09:00')) ?>" required /></p>
			<p><label>Ora Fine:</label> <input type="time" name="orario_fine"
					value="<?= date('H:i', strtotime($corso->orario_fine ?? '10:00')) ?>" required /></p>
			<p><label>Durata (minuti):</label> <input type="text"
					value="<?= (int) ($corso->durata ?? 0) ?> (calcolato al salvataggio)" readonly
					style="background: #2a2a2a; color: #888;" /></p>
			<p><label>Posti Totali:</label> <input type="number" name="posti_disponibili"
					value="<?= (int) ($corso->posti_disponibili ?? 20) ?>" min="1" max="100" required /></p>
			<p><label>Posti Occupati:</label> <input type="number" name="posti_occupati"
					value="<?= (int) ($corso->posti_occupati ?? 0) ?>" readonly style="background: #2a2a2a; color: #888;" />
			</p>
			<p><label>Livello:</label>
				<select name="livello" required>
					<option value="">Seleziona livello</option>
					<option value="Principiante" <?= ($corso->livello ?? '') == 'Principiante' ? 'selected' : '' ?>>
						Principiante</option>
					<option value="Intermedio" <?= ($corso->livello ?? '') == 'Intermedio' ? 'selected' : '' ?>>Intermedio
					</option>
					<option value="Avanzato" <?= ($corso->livello ?? '') == 'Avanzato' ? 'selected' : '' ?>>Avanzato</option>
					<option value="Esperto" <?= ($corso->livello ?? '') == 'Esperto' ? 'selected' : '' ?>>Esperto</option>
				</select>
			</p>
			<button type="submit">Salva</button>
		</form>
		<div class="back"><a href="corsi.php">Torna all'elenco</a></div>
	<?php endif; ?>

	<script>setTimeout(function () { var t = document.querySelector('.toast'); if (t) t.remove(); }, 3000);</script>
</body>

</html>