-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 05. Nov 2018 um 20:46
-- Server-Version: 10.1.36-MariaDB
-- PHP-Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `schulmanager`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `homework`
--

CREATE TABLE IF NOT EXISTS `homework` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `due_date` date NOT NULL,
  `timetable_id_fk` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `desciption` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timetable_id_fk` (`timetable_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `learning_goal`
--

CREATE TABLE IF NOT EXISTS `learning_goal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id_fk` int(11) NOT NULL,
  `goal` text NOT NULL,
  `reached` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id_fk` (`test_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mark`
--

CREATE TABLE IF NOT EXISTS `mark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mark` double NOT NULL,
  `test_id_fk` int(11) DEFAULT NULL,
  `semester_id_fk` int(11) NOT NULL,
  `subject_id_fk` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id_fk` (`test_id_fk`),
  KEY `mark_groop_id_fk` (`semester_id_fk`),
  KEY `subject_id_fk` (`subject_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `note`
--

CREATE TABLE IF NOT EXISTS `note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `notetext` text NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notebook_id_fk` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notebook_id_fk` (`notebook_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notebook`
--

CREATE TABLE IF NOT EXISTS `notebook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `user_id_fk` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_fk` (`user_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `semester`
--

CREATE TABLE IF NOT EXISTS `semester` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `semester_start` date NOT NULL,
  `semester_end` date DEFAULT NULL,
  `semester_name` varchar(50) DEFAULT NULL,
  `user_id_fk` int(11) NOT NULL,
  `definitiv` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id_fk` (`user_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `subject`
--

CREATE TABLE IF NOT EXISTS `subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `user_id_fk` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_fk` (`user_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mark_goal` double NOT NULL,
  `subject_id_fk` int(11) NOT NULL,
  `user_id_fk` int(11) NOT NULL,
  `topic` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id_fk` (`subject_id_fk`),
  KEY `user_id_fk` (`user_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `test_time`
--

CREATE TABLE IF NOT EXISTS `test_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id_fk` int(11) NOT NULL,
  `timetable_id_fk` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id_fk` (`test_id_fk`),
  KEY `timetable_id_fk` (`timetable_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `timetable`
--

CREATE TABLE IF NOT EXISTS `timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id_fk` int(11) NOT NULL,
  `subject_id_fk` int(11) NOT NULL,
  `day_of_week` char(2) NOT NULL,
  `start_at` time NOT NULL,
  `end_at` time NOT NULL,
  `teacher_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_fk` (`user_id_fk`),
  KEY `subject_id_fk` (`subject_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` char(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `homework`
--
ALTER TABLE `homework`
  ADD CONSTRAINT `fk_timetable` FOREIGN KEY (`timetable_id_fk`) REFERENCES `timetable` (`id`);

--
-- Constraints der Tabelle `learning_goal`
--
ALTER TABLE `learning_goal`
  ADD CONSTRAINT `learning_goal_test_fk` FOREIGN KEY (`test_id_fk`) REFERENCES `test` (`id`);

--
-- Constraints der Tabelle `mark`
--
ALTER TABLE `mark`
  ADD CONSTRAINT `mark_test_fk` FOREIGN KEY (`test_id_fk`) REFERENCES `test` (`id`),
  ADD CONSTRAINT `semester_id_fk` FOREIGN KEY (`semester_id_fk`) REFERENCES `semester` (`id`),
  ADD CONSTRAINT `subject_id_fk` FOREIGN KEY (`subject_id_fk`) REFERENCES `subject` (`id`);

--
-- Constraints der Tabelle `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `notebook_fk` FOREIGN KEY (`notebook_id_fk`) REFERENCES `notebook` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `notebook`
--
ALTER TABLE `notebook`
  ADD CONSTRAINT `user_fk` FOREIGN KEY (`user_id_fk`) REFERENCES `user` (`id`);

--
-- Constraints der Tabelle `semester`
--
ALTER TABLE `semester`
  ADD CONSTRAINT `user_id_fk` FOREIGN KEY (`user_id_fk`) REFERENCES `user` (`id`);

--
-- Constraints der Tabelle `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `subject_user_fk` FOREIGN KEY (`user_id_fk`) REFERENCES `user` (`id`);

--
-- Constraints der Tabelle `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_subject_fk` FOREIGN KEY (`subject_id_fk`) REFERENCES `subject` (`id`),
  ADD CONSTRAINT `test_user_fk` FOREIGN KEY (`user_id_fk`) REFERENCES `user` (`id`);

--
-- Constraints der Tabelle `test_time`
--
ALTER TABLE `test_time`
  ADD CONSTRAINT `test_fk` FOREIGN KEY (`test_id_fk`) REFERENCES `test` (`id`),
  ADD CONSTRAINT `timetable_fk` FOREIGN KEY (`timetable_id_fk`) REFERENCES `timetable` (`id`);

--
-- Constraints der Tabelle `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_subject_fk` FOREIGN KEY (`subject_id_fk`) REFERENCES `subject` (`id`),
  ADD CONSTRAINT `timetable_user_fk` FOREIGN KEY (`user_id_fk`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
