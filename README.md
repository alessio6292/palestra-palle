# 🏋️ PALESTRA PALLE - Sistema Gestionale con AI

## 🚀 INSTALLAZIONE RAPIDA (Plug & Play)

### Requisiti Minimi
- Windows 10/11
- 8GB RAM minimo (16GB consigliato per AI)
- 10GB spazio libero

---

## 📦 SETUP AUTOMATICO (3 PASSI)

### 1️⃣ Installa XAMPP
1. Scarica XAMPP: https://www.apachefriends.org/download.html
2. Installa in `C:\xampp`
3. Avvia XAMPP Control Panel
4. Clicca **START** su Apache e MySQL

### 2️⃣ Copia i File
1. Copia la cartella `palestra palle` in `C:\xampp\htdocs\`
2. Path finale: `C:\xampp\htdocs\palestra palle\`

### 3️⃣ Importa Database
1. Apri browser: http://localhost/phpmyadmin
2. Clicca "Nuovo" → Nome: `palestra_palle`
3. Clicca "Importa" → Seleziona `palestra_palle.sql`
4. Clicca "Esegui"

---

## ⚡ AVVIO RAPIDO

### Metodo 1: Doppio Click
Clicca su `AVVIA.bat` - si avvia tutto automaticamente!

### Metodo 2: Manuale
1. Avvia XAMPP Control Panel
2. Start Apache + MySQL
3. Apri browser: http://localhost/palestra palle/

---

## 🤖 CONFIGURAZIONE AI (Opzionale)

Il sistema rileva automaticamente il tuo hardware e sceglie la modalità migliore!

### Setup AI Automatico
1. Nella pagina principale, clicca l'**icona rotellina** (⚙️) in alto a destra
2. Vedrai il tuo hardware rilevato automaticamente
3. Clicca "🚀 Installa AI (Auto-Setup)"
4. Aspetta 5-10 minuti
5. Fatto! L'AI è pronta

### Modalità Disponibili

#### 💡 **LIGHT** (Consigliata per CPU normali)
- Modello: phi3:mini (3.8B parametri)
- RAM necessaria: 4-8GB
- Velocità: ⚡⚡⚡ Veloce
- Funziona su qualsiasi PC moderno

#### 🔥 **POWER** (Per GPU potenti)
- Modello: llama3.2 (3B parametri)
- VRAM necessaria: 12-15GB
- Velocità: ⚡⚡ Media
- Richiede NVIDIA RTX 3060+ o AMD RX 6800+

**Il sistema sceglie automaticamente in base al tuo PC!**

---

## 📊 FUNZIONALITÀ

### ✅ Gestione Base
- **ISCRITTI**: Anagrafica completa membri palestra
- **CORSI**: Pianificazione corsi con orari e istruttori
- **GESTIONE CORSI**: Iscrizioni e disponibilità automatiche

### 🧠 Intelligenza Artificiale
- **Analisi Automatica**: Insights su pianificazione e saturazione corsi
- **Generatore Descrizioni**: Crea testi professionali per corsi
- **Suggerimenti Durata**: Calcola tempo ottimale basandosi su dati
- **Validazione Orari**: Rileva sovrapposizioni automaticamente
- **Raccomandazioni**: Suggerisce corsi personalizzati per iscritti

---

## 🎨 INTERFACCIA

- **Dark Mode** professionale tema palestra
- **Responsive**: funziona su desktop, tablet, mobile
- **Animazioni fluide** con effetti neon oro/giallo
- **Design moderno** stile bodybuilding

---

## 🔧 RISOLUZIONE PROBLEMI

### MySQL non parte
**Errore**: "Impossibile stabilire la connessione"
**Soluzione**: 
1. Apri XAMPP Control Panel
2. Clicca START su MySQL
3. Ricarica la pagina

### AI non funziona
**Problema**: Ollama non installato
**Soluzione**:
1. Clicca rotellina ⚙️ in alto
2. Clicca "Installa AI (Auto-Setup)"
3. Oppure manuale: esegui `AVVIA.bat` per setup completo

### Porta occupata
**Errore**: Apache non parte (porta 80 occupata)
**Soluzione**:
1. Chiudi Skype / IIS
2. Oppure cambia porta Apache nel config

---

## 📞 CONTATTI

Per supporto tecnico o domande, contatta l'amministratore del sistema.

---

## 🏆 CARATTERISTICHE AVANZATE

### Hardware Detection
Il sistema rileva automaticamente:
- GPU (NVIDIA/AMD) e VRAM disponibile
- RAM totale
- CPU cores
- Seleziona modalità AI ottimale

### Auto-Setup
- Download automatico Ollama
- Installazione silenziosa
- Download modelli AI in background
- Zero configurazione manuale

### Plug & Play
- Dai il programma a chiunque
- Lo copia su PC
- Esegue AVVIA.bat
- Tutto funziona subito!

---

## 📝 NOTE

- I dati sono salvati localmente in MySQL
- Backup consigliato: esporta database da phpMyAdmin
- L'AI lavora offline (privacy totale)
- Nessun costo cloud o abbonamenti

---

## 🆕 AGGIORNAMENTO GESTIONE ISCRIZIONI

### ✅ Nuove Funzionalità Implementate

#### 1. Controllo Iscrizioni Duplicate
**Problema risolto**: Il sistema impedisce di iscrivere nuovamente un utente a un corso se ha già un'iscrizione attiva con abbonamento valido.

**Come funziona**:
- Quando si crea una nuova iscrizione, il sistema verifica automaticamente se esiste già un'iscrizione per quella persona a quel corso
- Controlla che l'abbonamento dell'iscritto non sia scaduto
- Mostra un messaggio d'errore dettagliato con nome utente, corso e data di scadenza

**Codice**:
```php
// In db.php
$duplicato = $db->ControllaIscrizioneDuplicata($idIscritto, $idCorso);
if($duplicato) {
    echo "⚠️ {$duplicato->Nome} è già iscritto al corso con abbonamento valido";
}
```

#### 2. Pulsante Rinnovo per Iscrizioni Scadute
**Funzionalità**: Pulsante "🔄 Rinnova" che appare automaticamente per iscrizioni con abbonamento scaduto.

**Caratteristiche**:
- Visibile solo se `Data_scadenza < oggi`
- Sostituisce il pulsante "Modifica"
- Aggiorna automaticamente:
  - `data_iscrizione` → data odierna
  - `stato_partecipazione` → "Confermato"
- Richiede conferma prima di rinnovare

**Esempio visivo**:
```
┌──────────────────────────────────────┐
│ Iscrizione: Marco Rossi - Yoga      │
│ Scadenza: ⚫ Scaduto                 │
│ [🔄 Rinnova] [Elimina]              │
└──────────────────────────────────────┘
```

#### 3. Rimozione Note per Nuove Iscrizioni
**Comportamento**: Il campo "Note Particolari" non è più visibile quando si crea una nuova iscrizione.

**Motivazione**: Le note sono utili solo per annotare particolarità emerse durante la frequenza del corso.

**Implementazione**:
- Nuove iscrizioni: campo nascosto, valore vuoto
- Modifiche: campo visibile e modificabile

```php
<?php if($iscrizione->ID_iscrizione > 0): ?>
    <label>Note Particolari:</label>
    <textarea name="note_particolari"><?=$note?></textarea>
<?php else: ?>
    <input type="hidden" name="note_particolari" value="">
<?php endif; ?>
```

#### 4. Badge Scadenza Visibili
**Nuova colonna**: "Scadenza Abbonamento" nella tabella gestione con badge colorati e animati.

**Colori e stati**:
- 🟢 **Verde** (`scadenza-ok`): Abbonamento valido (>7 giorni)
- 🟡 **Giallo** (`scadenza-warning`): In scadenza (6-7 giorni)
- 🟠 **Arancione** (`scadenza-danger`): Attenzione (4-5 giorni)
- 🔴 **Rosso animato** (`scadenza-critical`): Critico (0-3 giorni) - animazione pulse
- ⚫ **Grigio barrato** (`scadenza-scaduto`): Scaduto - pulsante Rinnova abilitato

### 🧪 Testing Automatico

#### Ambiente Virtuale Python
```powershell
# Crea ambiente virtuale
python -m venv venv

# Attiva ambiente (Windows)
.\venv\Scripts\Activate.ps1

# Installa dipendenze
pip install -r requirements.txt
```

#### Esegui Test
```powershell
python test_iscrizioni.py
```

**Test eseguiti**:
1. ✅ Controllo iscrizioni duplicate con abbonamento valido
2. ✅ Identificazione iscrizioni scadute
3. ✅ Statistiche badge scadenza (conteggio per colore)
4. ✅ Simulazione rinnovo iscrizione (con rollback)

**Output esempio**:
```
🧪 TEST AUTOMATIZZATO - Sistema Gestione Iscrizioni

============================================================
  TEST 1: Controllo Iscrizioni Duplicate
============================================================
✅ PASS: Trovata iscrizione esistente per Mario Rossi
   Corso: Yoga Mattutino
   Scadenza: 2026-02-10
   ➡️ Il sistema dovrebbe impedire la ri-iscrizione

============================================================
  RIEPILOGO TEST
============================================================
   ✅ PASS: Controllo Duplicati
   ✅ PASS: Iscrizioni Scadute
   ✅ PASS: Statistiche
   ✅ PASS: Rinnovo

   Risultato finale: 4/4 test superati
   🎉 Tutti i test sono passati!
```

### 🗂️ Nuove Funzioni Database

#### `ControllaIscrizioneDuplicata($idIscritto, $idCorso)`
Verifica se un utente è già iscritto a un corso con abbonamento valido.

**Parametri**:
- `$idIscritto` - ID dell'iscritto
- `$idCorso` - ID del corso

**Ritorna**: `object|null` - Iscrizione esistente o null

**Esempio**:
```php
$dup = $db->ControllaIscrizioneDuplicata(1, 5);
if($dup) {
    echo "Già iscritto fino al {$dup->Data_scadenza}";
}
```

#### `GetIscrizioniConScadenza()`
Restituisce tutte le iscrizioni includendo la data di scadenza dell'abbonamento.

**Ritorna**: `array` - Array di oggetti iscrizione con campo `Data_scadenza`

**Query SQL**:
```sql
SELECT gc.*, i.Data_scadenza, i.Nome, i.Cognome, c.nome_corso
FROM gestione_corsi gc
JOIN iscritti i ON gc.ID_iscritti = i.ID_iscritti
JOIN corsi c ON gc.ID_corsi = c.ID_corsi
ORDER BY i.Data_scadenza ASC
```

#### `RinnovaIscrizione($idIscrizione)`
Rinnova un'iscrizione aggiornando data e stato.

**Parametri**:
- `$idIscrizione` - ID dell'iscrizione da rinnovare

**Ritorna**: `bool` - True se il rinnovo è riuscito

**Aggiornamenti**:
- `data_iscrizione` → CURDATE()
- `stato_partecipazione` → 'Confermato'

#### `GetIscrizioniScadute()`
Restituisce tutte le iscrizioni con abbonamento scaduto.

**Ritorna**: `array` - Array di iscrizioni scadute

**Condizione**: `i.Data_scadenza < CURDATE()`

### 📋 Flusso di Lavoro Aggiornato

#### Creazione Nuova Iscrizione
```mermaid
1. Utente clicca "Nuova Iscrizione"
   ↓
2. Seleziona iscritto e corso
   ↓
3. Sistema controlla duplicati
   ↓
4a. Se duplicato → Errore: "Già iscritto fino al [data]"
   ↓
4b. Se OK → Salva iscrizione (senza note)
   ↓
5. Messaggio successo: "✅ Iscrizione salvata!"
```

#### Gestione Iscrizione Scaduta
```mermaid
1. Tabella mostra badge ⚫ "Scaduto"
   ↓
2. Pulsante "🔄 Rinnova" visibile
   ↓
3. Utente clicca Rinnova
   ↓
4. Conferma: "Rinnovare questa iscrizione?"
   ↓
5. Sistema aggiorna data e stato
   ↓
6. Badge diventa 🟢 "Valido"
```

### 🎯 Vantaggi delle Nuove Funzionalità

1. **Prevenzione Errori**: Impossibile creare iscrizioni duplicate
2. **Efficienza**: Rinnovo rapido con un click
3. **Chiarezza**: Badge colorati mostrano stato a colpo d'occhio
4. **Pulizia Dati**: Note solo dove necessarie
5. **Testabilità**: Suite di test automatici completa

---

**Versione**: 2.0 - Gestione Iscrizioni Intelligente
**Data**: Gennaio 2026
**Testato**: ✅ 4/4 test superati
