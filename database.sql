-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 21 sep 2026 om 11:12
-- Serverversie: 8.4.2
-- PHP-versie: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schoolladder`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `classes`
--

CREATE TABLE `classes`
(
    `id`         int UNSIGNED                           NOT NULL,
    `name`       varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
    `created_at` timestamp                              NOT NULL,
    `updated_at` timestamp                              NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `events`
--

CREATE TABLE `events`
(
    `id`         int UNSIGNED                            NOT NULL,
    `start_time` datetime                                NOT NULL,
    `end_time`   datetime                                NOT NULL,
    `name`       varchar(50) COLLATE utf8mb4_general_ci  NOT NULL,
    `class_id`   int UNSIGNED                            NOT NULL,
    `teacher_id` int UNSIGNED                            NOT NULL,
    `subject_id` int UNSIGNED                            NOT NULL,
    `created_at` timestamp                               NOT NULL,
    `updated_at` timestamp                               NOT NULL,
    `location`   varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `grades`
--

CREATE TABLE `grades`
(
    `id`         int UNSIGNED NOT NULL,
    `student_id` int UNSIGNED NOT NULL,
    `subject_id` int UNSIGNED NOT NULL,
    `grade`      tinyint      NOT NULL,
    `created_at` timestamp    NOT NULL,
    `updated_at` timestamp    NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `students`
--

CREATE TABLE `students`
(
    `id`         int UNSIGNED NOT NULL,
    `user_id`    int UNSIGNED NOT NULL,
    `points`     tinyint      NOT NULL,
    `year_group` tinyint      NOT NULL,
    `class_id`   int UNSIGNED NOT NULL,
    `created_at` timestamp    NOT NULL,
    `updated_at` timestamp    NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `subjects`
--

CREATE TABLE `subjects`
(
    `id`           int UNSIGNED                           NOT NULL,
    `name`         varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
    `abbreviation` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
    `created_at`   timestamp                              NOT NULL,
    `updated_at`   timestamp                              NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tests`
--

CREATE TABLE `tests`
(
    `id`         int UNSIGNED NOT NULL,
    `event_id`   int UNSIGNED NOT NULL,
    `created_at` timestamp    NOT NULL,
    `updated_at` timestamp    NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users`
(
    `id`         int UNSIGNED                            NOT NULL,
    `name`       varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    `email`      varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    `password`   varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `role`       varchar(20) COLLATE utf8mb4_general_ci  NOT NULL,
    `created_at` timestamp                               NOT NULL,
    `updated_at` timestamp                               NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `classes`
--
ALTER TABLE `classes`
    ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `events`
--
ALTER TABLE `events`
    ADD PRIMARY KEY (`id`),
    ADD KEY `class_id` (`class_id`),
    ADD KEY `subject_id` (`subject_id`),
    ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexen voor tabel `grades`
--
ALTER TABLE `grades`
    ADD PRIMARY KEY (`id`),
    ADD KEY `student_id` (`student_id`),
    ADD KEY `subject_id` (`subject_id`);

--
-- Indexen voor tabel `students`
--
ALTER TABLE `students`
    ADD PRIMARY KEY (`id`),
    ADD KEY `class_id` (`class_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indexen voor tabel `subjects`
--
ALTER TABLE `subjects`
    ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `tests`
--
ALTER TABLE `tests`
    ADD PRIMARY KEY (`id`),
    ADD KEY `event_id` (`event_id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `classes`
--
ALTER TABLE `classes`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `events`
--
ALTER TABLE `events`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `grades`
--
ALTER TABLE `grades`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `students`
--
ALTER TABLE `students`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `subjects`
--
ALTER TABLE `subjects`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `tests`
--
ALTER TABLE `tests`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `events`
--
ALTER TABLE `events`
    ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
    ADD CONSTRAINT `events_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
    ADD CONSTRAINT `events_ibfk_4` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Beperkingen voor tabel `grades`
--
ALTER TABLE `grades`
    ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
    ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Beperkingen voor tabel `students`
--
ALTER TABLE `students`
    ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
    ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Beperkingen voor tabel `tests`
--
ALTER TABLE `tests`
    ADD CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
