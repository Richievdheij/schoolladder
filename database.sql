-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 29 sep 2026 om 11:04
-- Serverversie: 8.4.2
-- PHP-versie: 8.4.25

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
    `created_at` timestamp                              NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp                              NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `classes`
--

INSERT INTO `classes` (`id`, `name`, `created_at`, `updated_at`)
VALUES (1, '1A', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (2, '1B', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (3, '1C', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (4, '2A', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (5, '2B', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (6, '2C', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (7, '3A', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (8, '3B', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (9, '3C', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (10, '4A', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (11, '4B', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (12, '4C', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (13, '5A', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (14, '5B', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (15, '5C', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (16, '6A', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (17, '6B', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (18, '6C', '2026-09-29 09:26:34', '2026-09-29 09:26:34');

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
    `created_at` timestamp                               NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp                               NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
    `created_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `students`
--

CREATE TABLE `students`
(
    `id`              int UNSIGNED NOT NULL,
    `user_id`         int UNSIGNED NOT NULL,
    `points`          smallint     NOT NULL,
    `previous_points` smallint     NOT NULL,
    `year_group`      tinyint      NOT NULL,
    `class_id`        int UNSIGNED NOT NULL,
    `created_at`      timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `students`
--

INSERT INTO `students` (`id`, `user_id`, `points`, `previous_points`, `year_group`, `class_id`, `created_at`,
                        `updated_at`)
VALUES (1, 1, 1356, 1343, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (2, 2, 1350, 1346, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (3, 3, 1302, 1293, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (4, 4, 1285, 1280, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (5, 5, 1260, 1250, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (6, 6, 1248, 1225, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (7, 7, 1233, 1228, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (8, 8, 1210, 1205, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (9, 9, 1194, 1194, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (10, 10, 1150, 1140, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (11, 11, 1145, 1142, 4, 10, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (12, 12, 1369, 1345, 4, 12, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (13, 13, 1341, 1336, 1, 2, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (14, 14, 1290, 1286, 4, 12, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (15, 15, 1245, 1240, 4, 12, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (16, 16, 1330, 1325, 2, 6, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (17, 17, 1262, 1257, 4, 11, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (18, 18, 1299, 1284, 4, 11, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (19, 19, 1300, 1296, 5, 14, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (20, 20, 1375, 1360, 6, 17, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (21, 21, 1352, 1348, 6, 14, '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (22, 22, 1340, 1335, 3, 8, '2026-09-29 09:26:34', '2026-09-29 09:26:34');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `subjects`
--

CREATE TABLE `subjects`
(
    `id`           int UNSIGNED                           NOT NULL,
    `name`         varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
    `abbreviation` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
    `created_at`   timestamp                              NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   timestamp                              NOT NULL DEFAULT CURRENT_TIMESTAMP
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
    `created_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP
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
    `created_at` timestamp                               NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp                               NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`)
VALUES (1, 'Emma de Vries', 'emmadevries@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (2, 'Bas de Boot', 'basdeboot@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (3, 'John de Boer', 'johndeboer@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (4, 'Lars Jansen', 'larsjansen@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (5, 'Emily den Bosch', 'emilydenbosch@hr.nl', 'password', 'student', '2026-09-29 09:26:34',
        '2026-09-29 09:26:34'),
       (6, 'Noah Bakker', 'noahbakker@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (7, 'Anna van den Berg', 'annavandenberg@hr.nl', 'password', 'student', '2026-09-29 09:26:34',
        '2026-09-29 09:26:34'),
       (8, 'Jayden Smits', 'jaydensmits@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (9, 'Fatma Yilmaz', 'fatmayilmaz@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (10, 'Melisa Yilmaz', 'melisayilmaz@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (11, 'Ahmet Demir', 'ahmetdemir@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (12, 'James de Groot', 'jamesdegroot@hr.nl', 'password', 'student', '2026-09-29 09:26:34',
        '2026-09-29 09:26:34'),
       (13, 'Noor Vos', 'noorvos@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (14, 'Finn Kok', 'finnkok@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (15, 'Peter van Leeuwen', 'petervanleeuwen@hr.nl', 'password', 'student', '2026-09-29 09:26:34',
        '2026-09-29 09:26:34'),
       (16, 'Leo Peters', 'leopeters@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (17, 'Christina van Dijk', 'christinavandijk@hr.nl', 'password', 'student', '2026-09-29 09:26:34',
        '2026-09-29 09:26:34'),
       (18, 'Chris van Dijk', 'chrisvandijk@hr.nl', 'password', 'student', '2026-09-29 09:26:34',
        '2026-09-29 09:26:34'),
       (19, 'Tess van Dijk', 'tessvandijk@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (20, 'Klaas de Haan', 'klaasdehaan@hr.nl', 'password', 'student', '2026-09-29 09:26:34', '2026-09-29 09:26:34'),
       (21, 'Sarah Timmermans', 'sarahtimmermans@hr.nl', 'password', 'student', '2026-09-29 09:26:34',
        '2026-09-29 09:26:34'),
       (22, 'Saartje Timmermans', 'saartjetimmermans@hr.nl', 'password', 'student', '2026-09-29 09:26:34',
        '2026-09-29 09:26:34');

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
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 19;

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
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 23;

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
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 23;

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
