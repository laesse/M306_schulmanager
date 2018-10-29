-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 29. Okt 2018 um 19:56
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
-- Datenbank: `id7650771_schulmanager`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `homework`
--

CREATE TABLE `homework` (
  `id` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `timetable_id_fk` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `desciption` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `learning_goal`
--

CREATE TABLE `learning_goal` (
  `id` int(11) NOT NULL,
  `test_id_fk` int(11) NOT NULL,
  `goal` text NOT NULL,
  `reached` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mark`
--

CREATE TABLE `mark` (
  `id` int(11) NOT NULL,
  `mark` double NOT NULL,
  `subject_id_fk` int(11) NOT NULL,
  `test_id_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `note`
--

CREATE TABLE `note` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `notetext` text NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notebook_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notebook`
--

CREATE TABLE `notebook` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `user_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `subject`
--

CREATE TABLE `subject` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `mark_goal` double NOT NULL,
  `subject_id_fk` int(11) NOT NULL,
  `user_id_fk` int(11) NOT NULL,
  `topic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `test_time`
--

CREATE TABLE `test_time` (
  `id` int(11) NOT NULL,
  `test_id_fk` int(11) NOT NULL,
  `timetable_id_fk` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `user_id_fk` int(11) NOT NULL,
  `subject_id_fk` int(11) NOT NULL,
  `day_of_week` char(2) NOT NULL,
  `start_at` time NOT NULL,
  `end_at` time NOT NULL,
  `teacher_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` char(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `homework`
--
ALTER TABLE `homework`
  ADD PRIMARY KEY (`id`),
  ADD KEY `timetable_id_fk` (`timetable_id_fk`);

--
-- Indizes für die Tabelle `learning_goal`
--
ALTER TABLE `learning_goal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id_fk` (`test_id_fk`);

--
-- Indizes für die Tabelle `mark`
--
ALTER TABLE `mark`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id_fk` (`subject_id_fk`),
  ADD KEY `test_id_fk` (`test_id_fk`);

--
-- Indizes für die Tabelle `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notebook_id_fk` (`notebook_id_fk`);

--
-- Indizes für die Tabelle `notebook`
--
ALTER TABLE `notebook`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_fk` (`user_id_fk`);

--
-- Indizes für die Tabelle `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_fk` (`user_id_fk`);

--
-- Indizes für die Tabelle `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id_fk` (`subject_id_fk`),
  ADD KEY `user_id_fk` (`user_id_fk`);

--
-- Indizes für die Tabelle `test_time`
--
ALTER TABLE `test_time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id_fk` (`test_id_fk`),
  ADD KEY `timetable_id_fk` (`timetable_id_fk`);

--
-- Indizes für die Tabelle `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_fk` (`user_id_fk`),
  ADD KEY `subject_id_fk` (`subject_id_fk`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `homework`
--
ALTER TABLE `homework`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `learning_goal`
--
ALTER TABLE `learning_goal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `mark`
--
ALTER TABLE `mark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `note`
--
ALTER TABLE `note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `notebook`
--
ALTER TABLE `notebook`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `subject`
--
ALTER TABLE `subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `test_time`
--
ALTER TABLE `test_time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `mark_subject_fk` FOREIGN KEY (`subject_id_fk`) REFERENCES `subject` (`id`),
  ADD CONSTRAINT `mark_test_fk` FOREIGN KEY (`test_id_fk`) REFERENCES `test` (`id`);

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
