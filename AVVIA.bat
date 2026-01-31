@echo off
REM ============================================================
REM  PALESTRA PALLE - Avvio Sistema Intelligente
REM ============================================================
REM  Questo script:
REM  1. Rileva se è la prima esecuzione
REM  2. Se necessario, installa automaticamente l'AI
REM  3. Avvia tutti i servizi necessari
REM ============================================================

title Palestra Palle - Sistema Gestionale

echo.
echo ==================================================
echo   PALESTRA PALLE - Sistema Gestionale con AI
echo ==================================================
echo.

REM === CONTROLLO E CONFIGURAZIONE OLLAMA ===
REM Cerca Ollama nel percorso di installazione standard (non usa 'where' che può fallire)
set "OLLAMA_PATH=%LOCALAPPDATA%\Programs\Ollama"
set "OLLAMA_EXE=%OLLAMA_PATH%\ollama.exe"

REM Aggiungi Ollama al PATH della sessione corrente (se esiste)
if exist "%OLLAMA_EXE%" (
    set "PATH=%OLLAMA_PATH%;%PATH%"
    echo [OK] Ollama trovato in %OLLAMA_PATH%
) else (
    echo [!] Ollama non trovato - Avvio installazione AUTOMATICA...
    echo.
    call :INSTALLA_AI
    REM Dopo installazione, aggiungi al PATH
    if exist "%OLLAMA_EXE%" (
        set "PATH=%OLLAMA_PATH%;%PATH%"
    )
)

echo.

REM === AVVIO OLLAMA ===
echo [*] Avvio sistema AI...
start /B "" "%OLLAMA_EXE%" serve >nul 2>&1
timeout /t 3 /nobreak >nul

REM === AVVIO XAMPP ===
echo [*] Avvio XAMPP...
if exist "C:\xampp\xampp-control.exe" (
    start "" "C:\xampp\xampp-control.exe"
    echo [OK] XAMPP avviato
) else (
    echo [!] XAMPP non trovato in C:\xampp
    echo [!] Assicurati che XAMPP sia installato correttamente
)

REM Attendi che MySQL sia pronto
echo [*] Attesa database MySQL...
timeout /t 5 /nobreak >nul

REM === APERTURA BROWSER ===
echo [*] Apertura interfaccia nel browser...
start "" "http://localhost/palestra palle (3)/Index.php"

echo.
echo ==================================================
echo   SISTEMA PRONTO!
echo ==================================================
echo.
echo   Aperti:
echo   - XAMPP Control Panel
echo   - Browser su http://localhost/palestra palle/
echo.
echo   Per configurare AI: clicca rotellina in alto
echo.
echo   Premi un tasto per chiudere questa finestra...
pause >nul
exit /b



REM ============================================================
REM  FUNZIONE: INSTALLA AI
REM ============================================================
:INSTALLA_AI
echo.
echo --------------------------------------------------
echo   INSTALLAZIONE AI IN CORSO
echo --------------------------------------------------
echo.

REM --- SCARICA OLLAMA O USA LOCALE ---
if exist "installers\OllamaSetup.exe" (
    echo [INFO] Trovato installer locale in 'installers\OllamaSetup.exe'
    echo [1/3] Preparazione installazione locale...
    copy /Y "installers\OllamaSetup.exe" "%TEMP%\OllamaSetup.exe" >nul
) else (
    echo [INFO] Installer locale non trovato
    echo [1/3] Scaricamento Ollama da internet...
    powershell -Command "& {Invoke-WebRequest -Uri 'https://ollama.com/download/OllamaSetup.exe' -OutFile '%TEMP%\OllamaSetup.exe'}"
)

if not exist "%TEMP%\OllamaSetup.exe" (
    echo [ERRORE] Download Ollama fallito!
    echo [!] Controlla la connessione internet
    pause
    exit /b 1
)

REM --- INSTALLA OLLAMA ---
echo [2/3] Installazione Ollama (attendere)...
"%TEMP%\OllamaSetup.exe" /S
timeout /t 15 /nobreak >nul

REM Verifica installazione usando il percorso diretto (non 'where' che fallisce)
if not exist "%OLLAMA_EXE%" (
    echo [ERRORE] Installazione Ollama fallita!
    echo [!] File non trovato: %OLLAMA_EXE%
    pause
    exit /b 1
)

echo [OK] Ollama installato!

REM Aggiungi al PATH della sessione corrente
set "PATH=%OLLAMA_PATH%;%PATH%"

REM --- AVVIA OLLAMA ---
echo [*] Avvio servizio Ollama...
start /B "" "%OLLAMA_EXE%" serve
timeout /t 5 /nobreak >nul

REM --- SCARICA MODELLO BASE ---
echo [3/3] Download modello AI (phi3:mini - 2.3GB)...
echo [*] Questo passaggio puo' richiedere alcuni minuti...
"%OLLAMA_EXE%" pull phi3:mini

if %ERRORLEVEL% EQU 0 (
    echo [OK] Modello phi3:mini scaricato!
) else (
    echo [!] Download modello fallito - riprova dalla dashboard AI
)

echo.
echo --------------------------------------------------
echo   INSTALLAZIONE AI COMPLETATA!
echo --------------------------------------------------
echo.
echo   Il modello base (phi3:mini) e' pronto.
echo.
echo   NOTA: Se devi portare questo progetto su un altro PC senza internet,
echo   copia 'OllamaSetup.exe' nella cartella 'installers'.
echo.
exit /b



REM ============================================================
REM  NOTE PER GLI SVILUPPATORI
REM ============================================================
REM  Questo script sostituisce:
REM  - AVVIA.bat (vecchio)
REM  - setup_ai.bat (eliminato)
REM  
REM  Modifiche possibili:
REM  - Cambia porta XAMPP modificando il link browser
REM  - Aggiungi altri modelli nella sezione INSTALLA_AI
REM  - Modifica timeout per PC piu' lenti
REM ============================================================
