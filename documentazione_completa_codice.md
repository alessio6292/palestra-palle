# Documentazione Completa del Codice - Palestra Palle

Questa documentazione spiega in dettaglio ogni aspetto del codice del progetto "Palestra Palle", un sistema di gestione per una palestra con funzionalità AI integrate. Poiché si presume che il lettore non abbia conoscenze pregresse, ogni concetto sarà spiegato passo dopo passo, dalla base. Il codice è scritto in PHP, HTML, CSS e JavaScript, e utilizza un database MySQL.

## Struttura Generale del Progetto

Il progetto è un'applicazione web che gira su un server locale (XAMPP). Gestisce iscritti, corsi, e usa l'AI (Ollama) per generare contenuti intelligenti.

### File Principali
- `Index.php`: Pagina principale.
- `db.php`: Gestione database.
- `palestra_ai.php`: Classe per l'AI.
- `api_ai.php`: API per azioni AI.
- `ai_dashboard.php`: Dashboard per l'AI.
- Altri file per installazione, stili, etc.

## 1. db.php - Gestione del Database

Questo file contiene la classe `DB` che gestisce tutte le interazioni con il database MySQL.

### Classe DB
La classe `DB` si connette al database nel costruttore.

#### Costruttore `__construct()`
```php
function __construct(){
    $this->db = new mysqli('localhost', 'root', '', 'palestra_palle');
    if ($this->db->connect_error) {
        die("Errore di connessione: " . $this->db->connect_error);
    }
    $this->db->set_charset("utf8");
}
```
- Crea una connessione MySQL al database 'palestra_palle' su localhost con utente 'root' senza password.
- Se la connessione fallisce, ferma il programma con un messaggio di errore.
- Imposta il charset a UTF-8 per supportare caratteri speciali.

#### Metodo `GetIscritto()`
```php
function GetIscritto(){
    $sql = "SELECT * FROM iscritti";
    $res = $this->db->query($sql);
    $out = [];
    while($row = $res->fetch_object()){
        $out[] = $row;
    }
    return $out;
}
```
- Esegue una query SQL per selezionare tutti i record dalla tabella 'iscritti'.
- Usa `query()` per eseguire la query e ottenere un result set.
- Cicla attraverso ogni riga con `fetch_object()`, che restituisce un oggetto con proprietà corrispondenti alle colonne.
- Aggiunge ogni oggetto a un array `$out`.
- Restituisce l'array di oggetti iscritti.

#### Metodo `GetIscrittoId($id)`
```php
function GetIscrittoId($id){
    $id = $this->db->real_escape_string($id);
    $sql = "SELECT * FROM iscritti WHERE ID_iscritti = '$id'";
    $res = $this->db->query($sql);
    return $res->fetch_object();
}
```
- Prende un ID come parametro.
- Usa `real_escape_string()` per prevenire SQL injection, sanitizzando l'input.
- Costruisce una query per selezionare l'iscritto con quell'ID.
- Esegue la query e restituisce l'oggetto della riga, o null se non trovato.

#### Metodo `SalvaIscritto($iscritto)`
Questo metodo salva un iscritto, inserendo o aggiornando.
- Se `$iscritto->ID` esiste, fa UPDATE, altrimenti INSERT.
- Costruisce la query dinamicamente, escapando i valori.
- Esegue la query.

(Simile per altri metodi come GetCorsi, etc.)

## 2. palestra_ai.php - Sistema Intelligenza Artificiale

Questa classe gestisce l'AI usando Ollama.

### Variabili Principali
- `$db`: Connessione al database.
- `$ollamaAttivo`: Booleano se Ollama è attivo.
- `$modelloCorrente`: Stringa del modello AI, fisso a 'phi3:mini'.

### Costruttore `__construct($database)`
- Imposta `$this->db = $database`.
- Chiama `rilevaHardware()` per rilevare specs del PC.
- Chiama `controllaOllama()` per verificare se Ollama è in esecuzione.

### Metodo `rilevaHardware()`
Rileva hardware usando comandi Windows.
- Per GPU: Usa `wmic` per ottenere nome e VRAM.
- Per RAM: `wmic ComputerSystem get TotalPhysicalMemory`.
- Per CPU: `wmic cpu get NumberOfCores`.
- Salva in `$this->infoGPU`, `$this->ramMB`, `$this->numeroCPU`.

### Metodo `controllaOllama()`
- Fa una richiesta curl a `http://127.0.0.1:11434/api/tags`.
- Se HTTP code 200, `$this->ollamaAttivo = true`.

### Metodo `chat($domanda)`
- Controlla se DB è connesso.
- Controlla se Ollama attivo.
- Controlla se modello scaricato.
- Costruisce contesto con `costruisciContesto()`.
- Chiama `chatConOllama()`.

### Metodo `costruisciContesto()`
- Crea una stringa con dati palestra: iscritti attivi, in scadenza, corsi con occupazione.

### Metodo `chatConOllama($domanda, $contesto)`
- Costruisce un prompt con contesto e domanda.
- Chiama `chiamaOllama()` con il prompt.

### Metodo `chiamaOllama($prompt, $maxToken)`
- Prepara dati JSON per API Ollama.
- Fa richiesta POST a `http://127.0.0.1:11434/api/generate`.
- Gestisce retry in caso di fallimento.
- Restituisce la risposta.

### Metodo `generaDescrizione($nomeCorso, $livello, $durata)`
- Controlla Ollama.
- Costruisce prompt per descrizione corso.
- Chiama Ollama.

### Metodo `generaConsigli($idCliente)`
- Recupera dati cliente.
- Recupera corsi.
- Costruisce contesto cliente e corsi.
- Prompt per consigli.
- Chiama Ollama.

### Metodo `generaReport()`
- Costruisce contesto palestra.
- Prompt per report.
- Chiama Ollama.

## 3. api_ai.php - API per Azioni AI

Questo file gestisce richieste AJAX per AI.

### Logica Generale
- Riceve `azione` via GET/POST.
- Per 'status': Restituisce stato installazione da JSON.
- Per 'reset': Resetta stato.
- Per 'install': Avvia installazione in background.
- Per 'download': Scarica modello se valido (solo phi3:mini).
- Per 'chat': Chiama `palestra_ai->chat()`.

## 4. ai_dashboard.php - Dashboard AI

Pagina HTML con form per generare descrizioni, consigli, report.

- Include `palestra_ai.php` e `db.php`.
- Form per descrizione: input nome, livello, durata.
- Form per consigli: select cliente.
- Form per report: pulsante.
- Gestisce POST per generare e mostrare risultati.

## Altri File
- `Index.php`: Include navigazione e contenuto principale.
- `install_run.php`: Script per installare Ollama e modello.
- `style.css`: Stili CSS.
- `README.md`: Descrizione progetto.

Questa documentazione copre ogni funzione e concetto chiave. Per dettagli su righe specifiche, riferirsi al codice sorgente.</content>
<parameter name="filePath">c:\xampp\htdocs\palestra palle (3)\documentazione_codice.md