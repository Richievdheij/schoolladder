-- Schoolladder: the tables the dashboard still needs.
--
-- An addition to database.sql, not a replacement. Import it after that one,
-- because it references students, classes and events.
--
--     mysql -h 127.0.0.1 -u root schoolladder < database-dashboard.sql
--
-- Everything is current: a place is counted per request from students.points,
-- so the ladder is right again as soon as anyone's points change. Two tables
-- do keep history, both for the same reason - you cannot look back at what
-- you never wrote down.
--
--   point_categories + point_events   Waar je op beoordeeld wordt
--   standing_snapshots                Je stand and the trend in the hero
--   zones                             the zone tile and its margin
--   zone_checks                       the zone check in the first card
--   attendance_records                Aanwezigheid, the streak, Op tijd komen
--   badges + student_badges           the badges
--
-- Points stay invisible to students. These tables hold them - there is
-- nothing to rank without - but what the student pages show is a place, a
-- zone and a word. See the README.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- max_points is what there is to earn this period. That number turns a sum
-- into a band: which of the meter's steps you reach follows from earned over
-- available. The student sees neither number, only the word.
CREATE TABLE `point_categories` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `max_points` int UNSIGNED NOT NULL,
  `sort_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `point_categories` (`name`, `slug`, `max_points`, `sort_order`) VALUES
('Cijfers',       'grades',      500, 1),
('Aanwezigheid',  'attendance',  400, 2),
('Op tijd komen', 'punctuality', 280, 3),
('Groei',         'growth',      350, 4);

-- The ledger. One row per change, never edited or deleted, which is why there
-- is no updated_at: a correction is a new row with a negative amount.
--
-- This is what makes the system current. Nothing has to be recalculated,
-- because every change already wrote itself down with its moment attached.
-- students.points stays on as the running total so ranking remains one
-- comparison instead of a sum over this whole table.
CREATE TABLE `point_events` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `points` smallint NOT NULL,
  `reason` varchar(100) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_created` (`student_id`, `created_at`),
  KEY `student_category` (`student_id`, `category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `point_events_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `point_events_category` FOREIGN KEY (`category_id`) REFERENCES `point_categories` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Your place at a moment. For looking back only: your place now is counted,
-- not read from here. Write it with a periodic task, hourly is plenty:
--
--   INSERT INTO standing_snapshots (student_id, rank_position)
--   SELECT s.id, (SELECT COUNT(*) + 1 FROM students x WHERE x.points > s.points)
--     FROM students s;
--
-- "rank" cannot be a column name: it is reserved from MySQL 8 onwards.
CREATE TABLE `standing_snapshots` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` int UNSIGNED NOT NULL,
  `rank_position` smallint UNSIGNED NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_recorded` (`student_id`, `recorded_at`),
  CONSTRAINT `standing_snapshots_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Your zone is the one with the highest min_points still under your total.
-- The margin between the two becomes a word in PHP, because it is a points
-- difference and students do not get to see those.
CREATE TABLE `zones` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `letter` char(1) NOT NULL,
  `min_points` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `letter` (`letter`),
  KEY `min_points` (`min_points`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `zones` (`letter`, `min_points`) VALUES
('A', 1400),
('B', 1200),
('C', 900),
('D', 0);

-- An empty class_id means the whole year group, an empty year_group as well
-- means the whole school.
CREATE TABLE `zone_checks` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `year_group` tinyint UNSIGNED DEFAULT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `note` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `scheduled_at` (`scheduled_at`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `zone_checks_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- One row per student per lesson, written the moment they check in. Hence the
-- unique key: checking in twice for the same lesson should not be possible.
-- minutes_late feeds "Op tijd komen" - present but late is not absent.
CREATE TABLE `attendance_records` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` int UNSIGNED NOT NULL,
  `event_id` int UNSIGNED NOT NULL,
  `status` enum('present','late','absent') NOT NULL,
  `minutes_late` smallint UNSIGNED NOT NULL DEFAULT 0,
  `recorded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_event` (`student_id`, `event_id`),
  KEY `student_recorded` (`student_id`, `recorded_at`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `attendance_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `attendance_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- `icon` is the file name in images/icons without .svg, so no lookup table is
-- needed in PHP.
CREATE TABLE `badges` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `icon` varchar(30) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `sort_order` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `badges` (`name`, `icon`, `description`, `sort_order`) VALUES
('Vroege vogel',  'flame',        'Tien lessen op rij op tijd binnen.',  1),
('Perfecte week', 'circle-check', 'Een volle week zonder gemiste les.',  2),
('Stijger',       'trending-up',  'Vijf plekken gestegen.',              3),
('Top 10',        'trophy',       'Bij de eerste tien van je jaarlaag.', 4);

CREATE TABLE `student_badges` (
  `student_id` int UNSIGNED NOT NULL,
  `badge_id` int UNSIGNED NOT NULL,
  `earned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`student_id`, `badge_id`),
  KEY `badge_id` (`badge_id`),
  CONSTRAINT `student_badges_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `student_badges_badge` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Changes to existing tables. Not a preference but a necessity: without these
-- the database cannot store what the dashboard asks for.
--
--   students.points  tinyint stops at 127, so a score of 1.248 is silently
--                    truncated.
--   grades.grade     tinyint cannot hold an 8,4.
--   grades.kind      does not exist, so "Recent beoordeeld" cannot say
--                    whether something was a test or an assignment.
ALTER TABLE `students`
  MODIFY `points` int NOT NULL DEFAULT 0,
  ADD KEY `points` (`points`);

ALTER TABLE `grades`
  MODIFY `grade` decimal(3,1) NOT NULL,
  ADD COLUMN `kind` enum('toets','opdracht','practicum') NOT NULL DEFAULT 'toets' AFTER `grade`,
  MODIFY `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  ADD KEY `student_created` (`student_id`, `created_at`);

COMMIT;
