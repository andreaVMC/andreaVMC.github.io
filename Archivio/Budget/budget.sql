-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Dic 01, 2023 alle 18:39
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
-- Database: `budget`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `categorie`
--

CREATE TABLE `categorie` (
  `Id` int(11) NOT NULL,
  `nome` text NOT NULL,
  `grado` int(11) NOT NULL COMMENT '0=non necessario , 1=necessario, 2=investimento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `guadagni`
--

CREATE TABLE `guadagni` (
  `Id` int(11) NOT NULL,
  `valore` float NOT NULL,
  `data` date NOT NULL,
  `etichetta` text NOT NULL,
  `stato` int(11) NOT NULL COMMENT '0=ricevuto , 1=attesa, 2=previsto\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `spese`
--

CREATE TABLE `spese` (
  `Id` int(11) NOT NULL,
  `etichetta` text NOT NULL,
  `Id_categoria` int(11) NOT NULL,
  `valore` float NOT NULL,
  `data` date NOT NULL,
  `stato` int(11) NOT NULL COMMENT '0=ricevuto, 1=attesa, 2=previsto\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`Id`);

--
-- Indici per le tabelle `guadagni`
--
ALTER TABLE `guadagni`
  ADD PRIMARY KEY (`Id`);

--
-- Indici per le tabelle `spese`
--
ALTER TABLE `spese`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `categorie`
--
ALTER TABLE `categorie`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `guadagni`
--
ALTER TABLE `guadagni`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `spese`
--
ALTER TABLE `spese`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
