-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 10, 2026 alle 10:53
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `palestra_palle`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `corsi`
--

CREATE TABLE `corsi` (
  `ID_corsi` int(8) UNSIGNED NOT NULL,
  `nome_corso` varchar(20) NOT NULL,
  `istruttore` varchar(160) NOT NULL,
  `giorno_settimana` int(10) NOT NULL,
  `orario_inizio` datetime NOT NULL,
  `orario_fine` datetime NOT NULL,
  `durata` time NOT NULL,
  `posti_disponibili` int(10) NOT NULL,
  `posti_occupati` int(10) NOT NULL,
  `livello` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `corsi`
--

INSERT INTO `corsi` (`ID_corsi`, `nome_corso`, `istruttore`, `giorno_settimana`, `orario_inizio`, `orario_fine`, `durata`, `posti_disponibili`, `posti_occupati`, `livello`) VALUES
(1, 'Yoga Mattutino', 'Elena Conti', 1, '2026-01-13 09:00:00', '2026-01-13 10:00:00', '01:00:00', 20, 15, 'Principiante'),
(2, 'Pilates', 'Marco Ferrari', 2, '2026-01-14 18:30:00', '2026-01-14 19:30:00', '01:00:00', 15, 12, 'Intermedio'),
(3, 'Spinning', 'Luca Martini', 3, '2026-01-15 19:00:00', '2026-01-15 20:00:00', '01:00:00', 25, 20, 'Avanzato'),
(4, 'Zumba', 'Sofia Romano', 4, '2026-01-16 18:00:00', '2026-01-16 19:00:00', '01:00:00', 30, 25, 'Principiante'),
(5, 'gay training', 'Andrea Russo', 5, '2026-01-17 17:00:00', '2026-01-17 18:30:00', '01:30:00', 18, 16, 'Avanzato'),
(6, 'Yoga Serale', 'Elena Conti', 1, '2026-01-13 19:00:00', '2026-01-13 20:00:00', '01:00:00', 20, 18, 'Intermedio'),
(7, 'Functional', 'Marco Ferrari', 3, '2026-01-15 07:00:00', '2026-01-15 08:00:00', '01:00:00', 15, 10, 'Intermedio'),
(8, 'Stretching', 'Sofia Romano', 6, '2026-01-18 10:00:00', '2026-01-18 11:00:00', '01:00:00', 25, 8, 'Principiante'),
(9, 'Body Building', 'Andrea Russo', 2, '2026-01-14 20:00:00', '2026-01-14 21:00:00', '01:00:00', 12, 10, 'Avanzato'),
(10, 'Cardio Fit', 'Luca Martini', 5, '2026-01-17 08:00:00', '2026-01-17 09:00:00', '01:00:00', 20, 15, 'Intermedio');

-- --------------------------------------------------------

--
-- Struttura della tabella `gestione_corsi`
--

CREATE TABLE `gestione_corsi` (
  `ID_iscrizione` int(8) UNSIGNED NOT NULL,
  `ID_iscritti` int(8) UNSIGNED NOT NULL,
  `ID_corsi` int(8) UNSIGNED NOT NULL,
  `data_iscrizione` date NOT NULL,
  `stato_partecipazione` varchar(20) NOT NULL,
  `note_particolari` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `gestione_corsi`
--

INSERT INTO `gestione_corsi` (`ID_iscrizione`, `ID_iscritti`, `ID_corsi`, `data_iscrizione`, `stato_partecipazione`, `note_particolari`) VALUES
(1, 1, 1, '2026-01-05', 'Confermato', ''),
(2, 1, 3, '2026-01-05', 'Confermato', ''),
(3, 2, 2, '2026-01-06', 'Confermato', 'Problemi alla schiena'),
(4, 2, 6, '2026-01-06', 'Confermato', ''),
(5, 3, 5, '2026-01-04', 'Confermato', ''),
(6, 3, 9, '2026-01-04', 'Confermato', ''),
(7, 4, 4, '2025-11-15', 'Lista d\'attesa', 'Abbonamento scaduto'),
(8, 5, 1, '2026-01-07', 'Confermato', ''),
(9, 5, 8, '2026-01-07', 'Confermato', ''),
(10, 6, 2, '2026-01-08', 'Confermato', ''),
(11, 6, 4, '2026-01-08', 'Confermato', ''),
(12, 7, 3, '2026-01-03', 'Confermato', 'Preferisce orari serali'),
(13, 7, 5, '2026-01-03', 'Confermato', ''),
(14, 8, 6, '2026-01-09', 'Confermato', ''),
(15, 8, 7, '2026-01-09', 'Confermato', ''),
(16, 9, 10, '2026-01-08', 'Confermato', ''),
(17, 9, 3, '2026-01-08', 'Confermato', ''),
(18, 10, 4, '2026-01-09', 'Confermato', ''),
(19, 10, 8, '2026-01-09', 'Confermato', 'Prima esperienza in palestra');

-- --------------------------------------------------------

--
-- Struttura della tabella `iscritti`
--

CREATE TABLE `iscritti` (
  `ID_iscritti` int(8) UNSIGNED NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Cognome` varchar(100) NOT NULL,
  `Data_nascita` date NOT NULL,
  `Email` text NOT NULL,
  `Telefono` text NOT NULL,
  `Tipo_abbonamento` varchar(200) NOT NULL,
  `Data_scadenza` date NOT NULL,
  `Stato` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `iscritti`
--

INSERT INTO `iscritti` (`ID_iscritti`, `Nome`, `Cognome`, `Data_nascita`, `Email`, `Telefono`, `Tipo_abbonamento`, `Data_scadenza`, `Stato`) VALUES
(1, 'Mario', 'Rossi', '1990-05-15', 'mario.rossi@email.com', '3331234567', 'Mensile', '2026-02-10', 'Attivo'),
(2, 'Laura', 'Bianchi', '1985-08-22', 'laura.bianchi@email.com', '3339876543', 'Trimestrale', '2026-04-10', 'Attivo'),
(3, 'Giuseppe', 'Verdi', '1992-11-30', 'giuseppe.verdi@email.com', '3335551234', 'Annuale', '2027-01-10', 'Attivo'),
(4, 'Anna', 'Neri', '1988-03-12', 'anna.neri@email.com', '3337778888', 'Mensile', '2025-12-31', 'Scaduto'),
(5, 'Paolo', 'Gialli', '1995-07-08', 'paolo.gialli@email.com', '3334445566', 'Semestrale', '2026-07-10', 'Attivo'),
(6, 'Francesca', 'Blu', '1991-01-25', 'francesca.blu@email.com', '3332223344', 'Mensile', '2026-02-10', 'Attivo'),
(7, 'Marco', 'Viola', '1987-09-18', 'marco.viola@email.com', '3336667788', 'Trimestrale', '2026-03-15', 'Attivo'),
(8, 'Giulia', 'Rosa', '1993-12-05', 'giulia.rosa@email.com', '3338889900', 'Annuale', '2026-12-20', 'Attivo'),
(9, 'Stefano', 'Grigi', '1994-06-20', 'stefano.grigi@email.com', '3331112233', 'Mensile', '2026-02-10', 'Attivo'),
(10, 'Chiara', 'Marroni', '1989-04-15', 'chiara.marroni@email.com', '3335554444', 'Trimestrale', '2026-03-20', 'Attivo');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `corsi`
--
ALTER TABLE `corsi`
  ADD PRIMARY KEY (`ID_corsi`);

--
-- Indici per le tabelle `gestione_corsi`
--
ALTER TABLE `gestione_corsi`
  ADD PRIMARY KEY (`ID_iscrizione`),
  ADD KEY `ID_iscritto` (`ID_iscritti`),
  ADD KEY `ID_corso` (`ID_corsi`);

--
-- Indici per le tabelle `iscritti`
--
ALTER TABLE `iscritti`
  ADD PRIMARY KEY (`ID_iscritti`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `corsi`
--
ALTER TABLE `corsi`
  MODIFY `ID_corsi` int(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `gestione_corsi`
--
ALTER TABLE `gestione_corsi`
  MODIFY `ID_iscrizione` int(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT per la tabella `iscritti`
--
ALTER TABLE `iscritti`
  MODIFY `ID_iscritti` int(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `gestione_corsi`
--
ALTER TABLE `gestione_corsi`
  ADD CONSTRAINT `gestione_corsi_ibfk_1` FOREIGN KEY (`ID_iscritti`) REFERENCES `iscritti` (`ID_iscritti`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gestione_corsi_ibfk_2` FOREIGN KEY (`ID_corsi`) REFERENCES `corsi` (`ID_corsi`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
