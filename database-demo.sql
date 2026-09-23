-- Schoolladder: twee demo-leerlingen om het dashboard mee te testen.
--
-- Importeer dit na database.sql en database-subjects.sql. Opnieuw draaien mag:
-- het bestand gooit eerst zijn eigen gegevens weg.
--
-- Inloggen op /portal/login/ met wachtwoord  demo1234
--
--   noa@demo.test   4H havo, staat er goed voor
--   sem@demo.test   4V vwo, staat onder druk
--
-- Alle datums zijn relatief aan vandaag, dus het blijft er live uitzien.
-- Dit bestand hoort niet op een echte installatie.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- Eerst weg wat een vorige keer is gezet. De foreign keys ruimen de rest op.
DELETE FROM `users` WHERE `email` LIKE '%@demo.test';
DELETE FROM `classes` WHERE `name` IN ('4H', '4V');
DELETE FROM `periods` WHERE `school_year` = '2026-2027';
DELETE FROM `zone_checks`;

INSERT INTO `periods` (`school_year`, `name`, `starts_on`, `ends_on`) VALUES
('2026-2027', 'Blok 1', DATE_SUB(CURDATE(), INTERVAL 40 DAY), DATE_ADD(CURDATE(), INTERVAL 30 DAY));

INSERT INTO `classes` (`name`, `level`, `created_at`, `updated_at`) VALUES
('4H', 'havo', NOW(), NOW()),
('4V', 'vwo', NOW(), NOW());

SET @klas4h = (SELECT id FROM `classes` WHERE `name` = '4H');
SET @klas4v = (SELECT id FROM `classes` WHERE `name` = '4V');

INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Mw. Dekker', 'docent1@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'teacher'),
('Dhr. Vos', 'docent2@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'teacher'),
('Mw. Aziz', 'docent3@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'teacher'),
('Dhr. Bakker', 'docent4@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'teacher'),
('Mw. Roos', 'docent5@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'teacher');
SET @doc1 = (SELECT id FROM `users` WHERE `email` = 'docent1@demo.test');
SET @doc2 = (SELECT id FROM `users` WHERE `email` = 'docent2@demo.test');
SET @doc3 = (SELECT id FROM `users` WHERE `email` = 'docent3@demo.test');
SET @doc4 = (SELECT id FROM `users` WHERE `email` = 'docent4@demo.test');
SET @doc5 = (SELECT id FROM `users` WHERE `email` = 'docent5@demo.test');

INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Jade Hofman', 'jade@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Lars Timmer', 'lars@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Sanne de Groot', 'sanne@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Milan Petrov', 'milan@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Noa de Vries', 'noa@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Yara Bakker', 'yara@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Tim Jansen', 'tim@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Imke Sluis', 'imke@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Jonas Wierda', 'jonas@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Sem Bakker', 'sem@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Ravi Nandoe', 'ravi@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student'),
('Fenna Groot', 'fenna@demo.test', '$2y$12$XhioCCS9T0IlyVMiQKvBzeWpMJv/iw27OnqylF1qzvNH9m9NsuRby', 'student');

INSERT INTO `students` (`user_id`, `points`, `year_group`, `class_id`, `created_at`, `updated_at`) VALUES
((SELECT id FROM `users` WHERE `email` = 'jade@demo.test'), 1420, 4, @klas4h, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'lars@demo.test'), 1310, 4, @klas4v, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'sanne@demo.test'), 1284, 4, @klas4h, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'milan@demo.test'), 1260, 4, @klas4v, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'noa@demo.test'), 1248, 4, @klas4h, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'yara@demo.test'), 1231, 4, @klas4h, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'tim@demo.test'), 1219, 4, @klas4v, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'imke@demo.test'), 1180, 4, @klas4h, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'jonas@demo.test'), 1100, 4, @klas4v, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'sem@demo.test'), 1045, 4, @klas4v, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'ravi@demo.test'), 980, 4, @klas4h, NOW(), NOW()),
((SELECT id FROM `users` WHERE `email` = 'fenna@demo.test'), 910, 4, @klas4v, NOW(), NOW());

SET @noa = (SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'noa@demo.test');
SET @sem = (SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'sem@demo.test');

INSERT INTO `student_subjects` (`student_id`, `subject_id`) VALUES
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1)),
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1)),
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'wiskunde B' LIMIT 1)),
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'scheikunde' LIMIT 1)),
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'biologie' LIMIT 1)),
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'geschiedenis' LIMIT 1)),
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'lichamelijke opvoeding' LIMIT 1)),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1)),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1)),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'wiskunde B' LIMIT 1)),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'natuurkunde' LIMIT 1)),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'Duits' LIMIT 1)),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'geschiedenis' LIMIT 1)),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'lichamelijke opvoeding' LIMIT 1));

INSERT INTO `events` (`start_time`, `end_time`, `name`, `class_id`, `teacher_id`, `subject_id`, `created_at`, `updated_at`, `location`) VALUES
(CONCAT(CURDATE(), ' 08:30:00'), CONCAT(CURDATE(), ' 09:20:00'), 'Nederlands', @klas4h, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(CURDATE(), ' 09:30:00'), CONCAT(CURDATE(), ' 10:20:00'), 'Engels', @klas4h, @doc2, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'A1.02'),
(CONCAT(CURDATE(), ' 10:45:00'), CONCAT(CURDATE(), ' 11:35:00'), 'wiskunde B', @klas4h, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'wiskunde B' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(CURDATE(), ' 12:15:00'), CONCAT(CURDATE(), ' 13:05:00'), 'scheikunde', @klas4h, @doc4, (SELECT id FROM `subjects` WHERE `name` = 'scheikunde' LIMIT 1), NOW(), NOW(), 'Gymzaal 2'),
(CONCAT(CURDATE(), ' 13:15:00'), CONCAT(CURDATE(), ' 14:05:00'), 'biologie', @klas4h, @doc5, (SELECT id FROM `subjects` WHERE `name` = 'biologie' LIMIT 1), NOW(), NOW(), 'B1.09'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 09:20:00'), 'Nederlands', @klas4h, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 11:35:00'), 'Engels', @klas4h, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 09:20:00'), 'Nederlands', @klas4h, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 11:35:00'), 'Engels', @klas4h, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 09:20:00'), 'Nederlands', @klas4h, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 11:35:00'), 'Engels', @klas4h, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 09:20:00'), 'Nederlands', @klas4h, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 11:35:00'), 'Engels', @klas4h, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 09:20:00'), 'Nederlands', @klas4h, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 11:35:00'), 'Engels', @klas4h, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 09:20:00'), 'Nederlands', @klas4h, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 11:35:00'), 'Engels', @klas4h, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 11 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 11 DAY), ' 09:20:00'), 'Nederlands', @klas4h, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 11 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 11 DAY), ' 11:35:00'), 'Engels', @klas4h, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 12 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 12 DAY), ' 09:20:00'), 'Nederlands', @klas4h, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 12 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 12 DAY), ' 11:35:00'), 'Engels', @klas4h, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(CURDATE(), ' 08:30:00'), CONCAT(CURDATE(), ' 09:20:00'), 'Nederlands', @klas4v, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(CURDATE(), ' 09:30:00'), CONCAT(CURDATE(), ' 10:20:00'), 'Engels', @klas4v, @doc2, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'A1.02'),
(CONCAT(CURDATE(), ' 10:45:00'), CONCAT(CURDATE(), ' 11:35:00'), 'wiskunde B', @klas4v, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'wiskunde B' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(CURDATE(), ' 12:15:00'), CONCAT(CURDATE(), ' 13:05:00'), 'natuurkunde', @klas4v, @doc4, (SELECT id FROM `subjects` WHERE `name` = 'natuurkunde' LIMIT 1), NOW(), NOW(), 'Gymzaal 2'),
(CONCAT(CURDATE(), ' 13:15:00'), CONCAT(CURDATE(), ' 14:05:00'), 'Duits', @klas4v, @doc5, (SELECT id FROM `subjects` WHERE `name` = 'Duits' LIMIT 1), NOW(), NOW(), 'B1.09'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 09:20:00'), 'Nederlands', @klas4v, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 11:35:00'), 'Engels', @klas4v, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 09:20:00'), 'Nederlands', @klas4v, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 11:35:00'), 'Engels', @klas4v, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 09:20:00'), 'Nederlands', @klas4v, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 11:35:00'), 'Engels', @klas4v, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 09:20:00'), 'Nederlands', @klas4v, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 11:35:00'), 'Engels', @klas4v, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 09:20:00'), 'Nederlands', @klas4v, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 11:35:00'), 'Engels', @klas4v, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 09:20:00'), 'Nederlands', @klas4v, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 11:35:00'), 'Engels', @klas4v, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 11 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 11 DAY), ' 09:20:00'), 'Nederlands', @klas4v, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 11 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 11 DAY), ' 11:35:00'), 'Engels', @klas4v, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 12 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 12 DAY), ' 09:20:00'), 'Nederlands', @klas4v, @doc1, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), NOW(), NOW(), 'B2.14'),
(CONCAT(DATE_SUB(CURDATE(), INTERVAL 12 DAY), ' 10:45:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 12 DAY), ' 11:35:00'), 'Engels', @klas4v, @doc3, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), NOW(), NOW(), 'C0.07');

-- Noa was er altijd en op tijd; Sem twee keer te laat en een keer afwezig.
INSERT INTO `attendance_records` (`student_id`, `event_id`, `status`, `minutes_late`, `recorded_at`)
SELECT @noa, e.id, 'present', 0, e.start_time
  FROM `events` e WHERE e.class_id = @klas4h AND e.start_time < NOW() - INTERVAL 1 DAY;

INSERT INTO `attendance_records` (`student_id`, `event_id`, `status`, `minutes_late`, `recorded_at`)
SELECT @sem, e.id,
       CASE WHEN e.id % 7 = 0 THEN 'late' WHEN e.id % 11 = 0 THEN 'absent' ELSE 'present' END,
       CASE WHEN e.id % 7 = 0 THEN 6 ELSE 0 END,
       e.start_time
  FROM `events` e WHERE e.class_id = @klas4v AND e.start_time < NOW() - INTERVAL 1 DAY;

INSERT INTO `grades` (`student_id`, `subject_id`, `grade`, `kind`, `period_id`, `created_at`, `updated_at`) VALUES
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'wiskunde B' LIMIT 1), 8.4, 'toets', (SELECT id FROM `periods` WHERE `school_year` = '2026-2027' LIMIT 1), DATE_SUB(NOW(), INTERVAL 0 DAY), NULL),
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), 6.8, 'opdracht', (SELECT id FROM `periods` WHERE `school_year` = '2026-2027' LIMIT 1), DATE_SUB(NOW(), INTERVAL 1 DAY), NULL),
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'scheikunde' LIMIT 1), 7.9, 'practicum', (SELECT id FROM `periods` WHERE `school_year` = '2026-2027' LIMIT 1), DATE_SUB(NOW(), INTERVAL 3 DAY), NULL),
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'Engels' LIMIT 1), 8.1, 'toets', (SELECT id FROM `periods` WHERE `school_year` = '2026-2027' LIMIT 1), DATE_SUB(NOW(), INTERVAL 8 DAY), NULL),
(@noa, (SELECT id FROM `subjects` WHERE `name` = 'biologie' LIMIT 1), 7.2, 'toets', (SELECT id FROM `periods` WHERE `school_year` = '2026-2027' LIMIT 1), DATE_SUB(NOW(), INTERVAL 14 DAY), NULL),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'Nederlands' LIMIT 1), 6.1, 'opdracht', (SELECT id FROM `periods` WHERE `school_year` = '2026-2027' LIMIT 1), DATE_SUB(NOW(), INTERVAL 1 DAY), NULL),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'natuurkunde' LIMIT 1), 4.8, 'toets', (SELECT id FROM `periods` WHERE `school_year` = '2026-2027' LIMIT 1), DATE_SUB(NOW(), INTERVAL 4 DAY), NULL),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'Duits' LIMIT 1), 5.4, 'toets', (SELECT id FROM `periods` WHERE `school_year` = '2026-2027' LIMIT 1), DATE_SUB(NOW(), INTERVAL 9 DAY), NULL),
(@sem, (SELECT id FROM `subjects` WHERE `name` = 'wiskunde B' LIMIT 1), 5.9, 'practicum', (SELECT id FROM `periods` WHERE `school_year` = '2026-2027' LIMIT 1), DATE_SUB(NOW(), INTERVAL 16 DAY), NULL);

INSERT INTO `point_events` (`student_id`, `category_id`, `points`, `reason`, `created_at`) VALUES
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'grades'), 215, '', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'grades'), 143, '', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'grades'), 72, '', DATE_SUB(NOW(), INTERVAL 27 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'attendance'), 170, '', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'attendance'), 113, '', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'attendance'), 57, '', DATE_SUB(NOW(), INTERVAL 27 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'punctuality'), 75, '', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'punctuality'), 50, '', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'punctuality'), 25, '', DATE_SUB(NOW(), INTERVAL 27 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'growth'), 164, '', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'growth'), 109, '', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(@noa, (SELECT id FROM `point_categories` WHERE `slug` = 'growth'), 55, '', DATE_SUB(NOW(), INTERVAL 27 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'grades'), 150, '', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'grades'), 100, '', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'grades'), 50, '', DATE_SUB(NOW(), INTERVAL 27 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'attendance'), 160, '', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'attendance'), 106, '', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'attendance'), 54, '', DATE_SUB(NOW(), INTERVAL 27 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'punctuality'), 52, '', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'punctuality'), 35, '', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'punctuality'), 18, '', DATE_SUB(NOW(), INTERVAL 27 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'growth'), 160, '', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'growth'), 106, '', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(@sem, (SELECT id FROM `point_categories` WHERE `slug` = 'growth'), 54, '', DATE_SUB(NOW(), INTERVAL 27 DAY));

INSERT INTO `standing_snapshots` (`student_id`, `rank_position`, `recorded_at`) VALUES
(@noa, 7, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(@noa, 7, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(@noa, 8, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(@noa, 8, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(@noa, 9, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(@noa, 9, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(@noa, 10, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(@noa, 12, DATE_SUB(NOW(), INTERVAL 42 DAY)),
(@noa, 11, DATE_SUB(NOW(), INTERVAL 35 DAY)),
(@noa, 10, DATE_SUB(NOW(), INTERVAL 28 DAY)),
(@noa, 9, DATE_SUB(NOW(), INTERVAL 21 DAY)),
(@noa, 8, DATE_SUB(NOW(), INTERVAL 14 DAY)),
(@noa, 24, DATE_SUB(NOW(), INTERVAL 6 MONTH)),
(@noa, 19, DATE_SUB(NOW(), INTERVAL 5 MONTH)),
(@noa, 16, DATE_SUB(NOW(), INTERVAL 4 MONTH)),
(@noa, 14, DATE_SUB(NOW(), INTERVAL 3 MONTH)),
(@noa, 12, DATE_SUB(NOW(), INTERVAL 2 MONTH)),
(@sem, 9, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(@sem, 9, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(@sem, 9, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(@sem, 9, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(@sem, 8, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(@sem, 8, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(@sem, 7, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(@sem, 6, DATE_SUB(NOW(), INTERVAL 42 DAY)),
(@sem, 7, DATE_SUB(NOW(), INTERVAL 35 DAY)),
(@sem, 7, DATE_SUB(NOW(), INTERVAL 28 DAY)),
(@sem, 8, DATE_SUB(NOW(), INTERVAL 21 DAY)),
(@sem, 8, DATE_SUB(NOW(), INTERVAL 14 DAY)),
(@sem, 4, DATE_SUB(NOW(), INTERVAL 6 MONTH)),
(@sem, 5, DATE_SUB(NOW(), INTERVAL 5 MONTH)),
(@sem, 6, DATE_SUB(NOW(), INTERVAL 4 MONTH)),
(@sem, 7, DATE_SUB(NOW(), INTERVAL 3 MONTH)),
(@sem, 8, DATE_SUB(NOW(), INTERVAL 2 MONTH)),
((SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'jade@demo.test'), 1, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
((SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'lars@demo.test'), 3, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
((SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'sanne@demo.test'), 2, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
((SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'milan@demo.test'), 4, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
((SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'yara@demo.test'), 6, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
((SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'tim@demo.test'), 8, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
((SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'imke@demo.test'), 8, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
((SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'jonas@demo.test'), 9, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
((SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'ravi@demo.test'), 12, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
((SELECT s.id FROM `students` s JOIN `users` u ON u.id = s.user_id WHERE u.email = 'fenna@demo.test'), 11, DATE_SUB(NOW(), INTERVAL 4 HOUR));

INSERT INTO `student_badges` (`student_id`, `badge_id`, `earned_at`)
SELECT @noa, id, DATE_SUB(NOW(), INTERVAL 5 DAY) FROM `badges` WHERE `name` IN ('Vroege vogel', 'Perfecte week', 'Stijger');

INSERT INTO `student_badges` (`student_id`, `badge_id`, `earned_at`)
SELECT @sem, id, DATE_SUB(NOW(), INTERVAL 20 DAY) FROM `badges` WHERE `name` = 'Stijger';

-- Een zonecontrole over drie dagen voor de hele school.
INSERT INTO `zone_checks` (`year_group`, `class_id`, `scheduled_at`, `note`) VALUES
(NULL, NULL, DATE_ADD(NOW(), INTERVAL 3 DAY), '2 waarschuwingen actief');
