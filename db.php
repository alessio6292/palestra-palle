<?PHP
	class DB {
		private $db;

		// Costruttore, connessione al database

		function __construct(){
			$this->db = new mysqli('localhost', 'root', '', 'palestra_palle');

		        // Verifica se la connessione è avvenuta con successo
	        if ($this->db->connect_error) {
            die("Errore di connessione: " . $this->db->connect_error);
        }
        
        // Imposta il charset per evitare problemi con caratteri speciali
        $this->db->set_charset("utf8");
				}

		//  GESTIONE iscritti
		// Restituisce tutti gli iscritti
		function GetIscritto(){
			$sql = "SELECT * FROM iscritti";
			$res = $this->db->query($sql);
			$out = [];
			while($row = $res->fetch_object()){
				$out[] = $row;
			}
			return $out;
		}

		// Restituisce un singolo iscritto dato l'ID
		function GetIscrittoId($id){
			$id = $this->db->real_escape_string($id);
			$sql = "SELECT * FROM iscritti WHERE ID_iscritti = '$id'";
			$res = $this->db->query($sql);
			return $res->fetch_object();
		}

		// Salva uno Iscritto (inserimento o modifica)
		function SalvaIscritto($iscritto){
			if($iscritto->ID){
				// Se ha ID, faccio UPDATE
				$sql = "UPDATE iscritti SET";
			}else{
				// se no INSERT
				$sql = "INSERT INTO iscritti SET";
			}
			$sql.=" Nome = '".$this->db->real_escape_string($iscritto->Nome)."', ";
			$sql.=" Cognome = '".$this->db->real_escape_string($iscritto->Cognome)."', ";
			$sql.=" Data_nascita = '".$this->db->real_escape_string($iscritto->Data_nascita)."', ";
			$sql.=" Email = '".$this->db->real_escape_string($iscritto->Email)."', ";
			$sql.=" Telefono = '".$this->db->real_escape_string($iscritto->Telefono)."', ";
			$sql.=" Tipo_abbonamento = '".$this->db->real_escape_string($iscritto->Tipo_abbonamento)."', ";
			$sql.=" Data_scadenza = '".$this->db->real_escape_string($iscritto->Data_scadenza)."', ";
			$sql.=" Stato = '".$this->db->real_escape_string($iscritto->Stato)."'";
			if($iscritto->ID){
				$sql.=" WHERE ID_iscritti = '".$this->db->real_escape_string($iscritto->ID)."'";
			}
			$this->db->query($sql);
		}

		// Cancella uno iscritto dato l'ID
		function CancellaIscritto($id){
			$id = $this->db->real_escape_string($id);
			$sql = "DELETE FROM iscritti WHERE ID_iscritti = '$id'";
			$this->db->query($sql);
		}

		//  GESTIONE CORSI
		
		// Restituisce tutti i corsi
		function GetCorsi(){
			$sql = "SELECT * FROM corsi";
			$res = $this->db->query($sql);
			$out = [];
			while($row = $res->fetch_object()){
				$out[] = $row;
			}
			return $out;
		}

		// Restituisce un singolo corso dato l'ID
		function GetCorso($id){
			$id = $this->db->real_escape_string($id);
			$sql = "SELECT * FROM corsi WHERE ID_corsi = '$id'";
			$res = $this->db->query($sql);
			return (object)$res->fetch_object();
		}

		// Salva un corso (inserimento o modifica)
		function SalvaCorso($corso){
			if($corso->ID){
				// Se ha ID, facciamo UPDATE
				$sql = "UPDATE corsi SET";
			}else{
				// Altrimenti INSERT
				$sql = "INSERT INTO corsi SET";
			}
			$sql.=" nome_corso = '".$this->db->real_escape_string($corso->nome_corso)."', ";
			$sql.=" istruttore = '".$this->db->real_escape_string($corso->istruttore)."', ";
			$sql.=" giorno_settimana = '".$this->db->real_escape_string($corso->giorno_settimana)."', ";
			$sql.=" orario_inizio = '".$this->db->real_escape_string($corso->orario_inizio)."', ";
			$sql.=" orario_fine = '".$this->db->real_escape_string($corso->orario_fine)."', ";
			$sql.=" durata = '".$this->db->real_escape_string($corso->durata)."', ";
			$sql.=" posti_disponibili = '".$this->db->real_escape_string($corso->posti_disponibili)."', ";
			$sql.=" posti_occupati = '".$this->db->real_escape_string($corso->posti_occupati)."', ";
			$sql.=" livello = '".$this->db->real_escape_string($corso->livello)."'";
			if($corso->ID){
				$sql.=" WHERE ID_corsi = '".$this->db->real_escape_string($corso->ID)."'";
			}
			$this->db->query($sql);
		}

		// Cancella un corso dato l'ID
		function CancellaCorso($id){
			$id = $this->db->real_escape_string($id);
			$sql = "DELETE FROM corsi WHERE ID_corsi = '$id'";
			$this->db->query($sql);
		}

		//  GESTIONE iscrizioni
		
		// Restituisce tutte le iscrizioni con i dati di iscritto e corso
		function GetIscrizioni(){
			$sql = "SELECT gc.*, i.Nome as iscritto_nome, i.Cognome as iscritto_cognome, 
						c.nome_corso as corso_nome_corso 
					FROM gestione_corsi gc
					JOIN iscritti i ON gc.ID_iscritti = i.ID_iscritti
					JOIN corsi c ON gc.ID_corsi = c.ID_corsi";
			$res = $this->db->query($sql);
			$out = [];
			while($row = $res->fetch_object()){
				$out[] = $row;
			}
			return $out;
		}
		
		// Restituisce tutte le iscrizioni con scadenza abbonamento
		function GetIscrizioniConScadenza(){
			$sql = "SELECT gc.*, i.Nome as iscritto_nome, i.Cognome as iscritto_cognome, 
						i.Data_scadenza, c.nome_corso as corso_nome_corso 
					FROM gestione_corsi gc
					JOIN iscritti i ON gc.ID_iscritti = i.ID_iscritti
					JOIN corsi c ON gc.ID_corsi = c.ID_corsi
					ORDER BY i.Data_scadenza ASC";
			$res = $this->db->query($sql);
			$out = [];
			while($row = $res->fetch_object()){
				$out[] = $row;
			}
			return $out;
		}

		// Restituisce un singolo iscrizione dato l'ID
		function GetIscrizione($id){
			$id = $this->db->real_escape_string($id);
			$sql = "SELECT * FROM gestione_corsi WHERE ID_iscrizione = '$id'";
			$res = $this->db->query($sql);
			return (object)$res->fetch_object();
		}

		// Salva un iscrizione ad un corso (inserimento o modifica)
		function SalvaIscrizione_corso($iscrizione_corso){
			if($iscrizione_corso->ID){
				// Se ha ID, faccio UPDATE
				$sql = "UPDATE gestione_corsi SET";
				$sql.=" ID_corsi = '".$this->db->real_escape_string($iscrizione_corso->ID_corsi)."', ";
				$sql.=" ID_iscritti = '".$this->db->real_escape_string($iscrizione_corso->ID_iscritti)."', ";
				$sql.=" data_iscrizione = '".$this->db->real_escape_string($iscrizione_corso->data_iscrizione)."', ";
				$sql.=" stato_partecipazione = '".$this->db->real_escape_string($iscrizione_corso->stato_partecipazione)."', ";
				$sql.=" note_particolari = '".$this->db->real_escape_string($iscrizione_corso->note_particolari)."'";
				$sql.=" WHERE ID_iscrizione = '".$this->db->real_escape_string($iscrizione_corso->ID)."'";
			}else{
				// Altrimenti INSERT (nuova iscrizione)
				$sql = "INSERT INTO gestione_corsi SET";
				$sql.=" ID_corsi = '".$this->db->real_escape_string($iscrizione_corso->ID_corsi)."', ";
				$sql.=" ID_iscritti = '".$this->db->real_escape_string($iscrizione_corso->ID_iscritti)."', ";
				$sql.=" data_iscrizione = '".$this->db->real_escape_string($iscrizione_corso->data_iscrizione)."', ";
				$sql.=" stato_partecipazione = 'Confermato', ";
				$sql.=" note_particolari = ''";
				
				// Aggiorna posti occupati del corso +1
				$this->db->query("UPDATE corsi SET posti_occupati = posti_occupati + 1 WHERE ID_corsi = '".$this->db->real_escape_string($iscrizione_corso->ID_corsi)."'");
			}
			$this->db->query($sql);
		}

		// Cancella un'iscrizione dato l'ID
		function CancellaIscrizione_corso($id){
			$iscrizione = $this->GetIscrizione($id);
			$id = $this->db->real_escape_string($id);
			$sql = "DELETE FROM gestione_corsi WHERE ID_iscrizione = '$id'";
			$this->db->query($sql);
			$this->db->query("UPDATE corsi SET posti_occupati = posti_occupati - 1 WHERE ID_corsi = '".$iscrizione->ID_corsi."'");
		}
		
		// Restituisce tutti i corsi a cui è iscritto un utente
		function GetCorsiByIscritto($idIscritto){
			$idIscritto = $this->db->real_escape_string($idIscritto);
			$sql = "SELECT c.*, gc.data_iscrizione, gc.stato_partecipazione, gc.note_particolari
					FROM gestione_corsi gc
					JOIN corsi c ON gc.ID_corsi = c.ID_corsi
					WHERE gc.ID_iscritti = '$idIscritto'
					ORDER BY c.giorno_settimana, c.orario_inizio";
			$res = $this->db->query($sql);
			$out = [];
			while($row = $res->fetch_object()){
				$out[] = $row;
			}
			return $out;
		}
		
		// Conta iscritti in scadenza (entro X giorni)
		function GetIscrittiInScadenza($giorni = 7){
			$sql = "SELECT * FROM iscritti 
					WHERE Data_scadenza BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL $giorni DAY)
					ORDER BY Data_scadenza ASC";
			$res = $this->db->query($sql);
			$out = [];
			while($row = $res->fetch_object()){
				$out[] = $row;
			}
			return $out;
		}
		
		function ControllaIscrizioneDuplicata($idIscritto, $idCorso){
			$idIscritto = $this->db->real_escape_string($idIscritto);
			$idCorso = $this->db->real_escape_string($idCorso);
			$sql = "SELECT gc.*, i.Data_scadenza, i.Nome, i.Cognome, c.nome_corso
					FROM gestione_corsi gc
					JOIN iscritti i ON gc.ID_iscritti = i.ID_iscritti
					JOIN corsi c ON gc.ID_corsi = c.ID_corsi
					WHERE gc.ID_iscritti = '$idIscritto' 
					AND gc.ID_corsi = '$idCorso'
					AND i.Data_scadenza >= CURDATE()
					LIMIT 1";
			$res = $this->db->query($sql);
			if($res && $res->num_rows > 0){
				return $res->fetch_object();
			}
			return null;
		}
		
		function GetIscrizioniScadute(){
			$sql = "SELECT gc.*, i.Data_scadenza, i.Nome, i.Cognome, c.nome_corso, c.giorno_settimana, c.orario_inizio
					FROM gestione_corsi gc
					JOIN iscritti i ON gc.ID_iscritti = i.ID_iscritti
					JOIN corsi c ON gc.ID_corsi = c.ID_corsi
					WHERE i.Data_scadenza < CURDATE()
					ORDER BY i.Data_scadenza DESC";
			$res = $this->db->query($sql);
			$out = [];
			while($row = $res->fetch_object()){
				$out[] = $row;
			}
			return $out;
		}
		
		function RinnovaIscrizione($idIscrizione){
			$idIscrizione = $this->db->real_escape_string($idIscrizione);
			$sql = "SELECT i.ID_iscritti, i.Tipo_abbonamento, i.Data_scadenza 
					FROM gestione_corsi gc
					JOIN iscritti i ON gc.ID_iscritti = i.ID_iscritti
					WHERE gc.ID_iscrizione = '$idIscrizione'";
			$res = $this->db->query($sql);
			$iscritto = $res->fetch_object();
			if(!$iscritto) return false;
			
			$intervallo = 'MONTH';
			$quantita = 1;
			$tipo = strtolower($iscritto->Tipo_abbonamento);
			if(strpos($tipo, 'mensile') !== false) { $intervallo = 'MONTH'; $quantita = 1; }
			elseif(strpos($tipo, 'trimestrale') !== false) { $intervallo = 'MONTH'; $quantita = 3; }
			elseif(strpos($tipo, 'semestrale') !== false) { $intervallo = 'MONTH'; $quantita = 6; }
			elseif(strpos($tipo, 'annuale') !== false) { $intervallo = 'YEAR'; $quantita = 1; }
			
			$dataPartenza = (strtotime($iscritto->Data_scadenza) < strtotime('today')) 
							? 'CURDATE()' 
							: "'" . $this->db->real_escape_string($iscritto->Data_scadenza) . "'";
			
			$sql = "UPDATE gestione_corsi SET data_iscrizione = CURDATE(), stato_partecipazione = 'Confermato' WHERE ID_iscrizione = '$idIscrizione'";
			$this->db->query($sql);
			$sql = "UPDATE iscritti SET Data_scadenza = DATE_ADD($dataPartenza, INTERVAL $quantita $intervallo), Stato = 'Attivo' WHERE ID_iscritti = '" . $this->db->real_escape_string($iscritto->ID_iscritti) . "'";
			return $this->db->query($sql);
		}
	}

	$db = new DB();
