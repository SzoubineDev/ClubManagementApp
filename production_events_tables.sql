-- =====================================================================
--  production_events_tables.sql
-- ---------------------------------------------------------------------
--  Event schema for the Club Management App, ready to apply to a
--  production MySQL 8 / MariaDB database.
--
--  Only TWO tables belong to the events feature:
--     events      — the event itself
--     event_user  — RSVP / attendance pivot (users <-> events)
--  Nothing else references `events` (verified in information_schema:
--  the only column with a foreign key to `events` is
--  `event_user.event_id`).
--
--  WHY YOUR PRODUCTION TABLES MAY BE "NOT CORRECT"
--  The project's own migrations record three real production failures:
--    1. `events` was created without `filiere`, while the model, the
--       form and the validation all use it →
--         SQLSTATE[42S22] Unknown column 'filiere'   (every save fails)
--    2. `status` was added by hand in phpMyAdmin as
--         ENUM('published','draft')   ← wrong order
--       which broke the `in:draft,published` validation on publish.
--    3. `status` / `published_at` missing entirely, so every event was
--       invisible on public pages.
--  Section 0 tells you which case you are in. Section 1 creates the
--  tables from scratch, Section 2 repairs an existing broken table,
--  Section 3 is the last-resort rebuild.
--
--  Requirements: `users` must already exist (InnoDB, BIGINT UNSIGNED id).
--  Charset/collation here match the application: utf8mb4_unicode_ci.
-- =====================================================================


-- ---------------------------------------------------------------------
-- 0. DIAGNOSTICS — run these first, they change nothing
-- ---------------------------------------------------------------------
-- Does the table exist at all?
-- SHOW TABLES LIKE 'events';
-- SHOW TABLES LIKE 'event_user';

-- What do the tables look like right now?
-- SHOW CREATE TABLE events;
-- SHOW CREATE TABLE event_user;

-- Column-by-column check. Compare with the reference in section 1.
-- SELECT column_name, column_type, is_nullable, column_default, extra
-- FROM information_schema.columns
-- WHERE table_schema = DATABASE() AND table_name = 'events'
-- ORDER BY ordinal_position;

-- Is `status` the correct enum order? Expect: enum('draft','published')
-- SELECT column_type FROM information_schema.columns
-- WHERE table_schema = DATABASE()
--   AND table_name = 'events' AND column_name = 'status';

-- Are the FK / unique / index constraints present?
-- SELECT constraint_name, constraint_type
-- FROM information_schema.table_constraints
-- WHERE table_schema = DATABASE() AND table_name IN ('events','event_user');
-- SELECT index_name, column_name, non_unique
-- FROM information_schema.statistics
-- WHERE table_schema = DATABASE() AND table_name IN ('events','event_user');


-- ---------------------------------------------------------------------
-- 1. FRESH CREATION — safe when the tables do not exist yet
-- ---------------------------------------------------------------------
-- `IF NOT EXISTS` means this section is a no-op on a database that
-- already has the table, so it will never destroy data.

CREATE TABLE IF NOT EXISTS `events` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`        VARCHAR(255)    NOT NULL,
    `description`  TEXT            NULL DEFAULT NULL,
    `location`     VARCHAR(255)    NULL DEFAULT NULL,
    `starts_at`    DATETIME        NOT NULL,
    `ends_at`      DATETIME        NULL DEFAULT NULL,
    `capacity`     INT UNSIGNED    NULL DEFAULT NULL,
    `team`         VARCHAR(255)    NULL DEFAULT NULL,
    `filiere`      VARCHAR(255)    NULL DEFAULT NULL,
    `status`       ENUM('draft','published') NOT NULL DEFAULT 'draft',
    `created_by`   BIGINT UNSIGNED NOT NULL,
    `published_at` TIMESTAMP       NULL DEFAULT NULL,
    `created_at`   TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`   TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `events_created_by_foreign` (`created_by`),
    CONSTRAINT `events_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Column notes
--   starts_at    required: the event date/time (a NULL here is what makes
--                event cards render blank / "null" in templates).
--   ends_at      optional.
--   capacity     optional seat limit; NULL = unlimited.
--   team         'arabic' | 'english' | 'french' | NULL (open to all).
--   filiere      free-text programme scoping; mutually exclusive with team.
--   status       enum in THIS order: 'draft', 'published'. The order
--                matters for the app's `in:draft,published` validation.
--   created_by   the author; FK to users, no cascade (deleting the author
--                is blocked while their events exist).
--   published_at timestamp set when the event is published; a past value
--                also counts as published in Event::isPublished().

CREATE TABLE IF NOT EXISTS `event_user` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_id`   BIGINT UNSIGNED NOT NULL,
    `user_id`    BIGINT UNSIGNED NOT NULL,
    `attended`   TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `event_user_event_id_user_id_unique` (`event_id`, `user_id`),
    KEY `event_user_user_id_foreign` (`user_id`),
    CONSTRAINT `event_user_event_id_foreign`
        FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `event_user_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Column notes
--   event_id + user_id  one RSVP row per member per event (unique).
--   attended            flipped by the attendance screen; 0 = RSVP only.
--   Both FKs cascade: deleting an event or a user removes the RSVP row.

-- Eloquent counterpart (app/Models/Event.php):
--   creator()   belongsTo User, foreign key created_by
--   attendees() belongsToMany User, pivot event_user, withPivot('attended')


-- ---------------------------------------------------------------------
-- 2. REPAIR AN EXISTING `events` TABLE
-- ---------------------------------------------------------------------
-- Use this instead of section 1 when `events` already exists but is
-- missing pieces. Every block is guarded by information_schema, so it
-- can be run as-is: if the change is already there, it does nothing.
-- Safe to run the whole section top to bottom.

-- 2.1 `filiere` — the cause of "Unknown column 'filiere'" on save.
SET @has := (SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE()
               AND table_name = 'events'
               AND column_name = 'filiere');
SET @sql := IF(@has = 0,
    'ALTER TABLE `events` ADD COLUMN `filiere` VARCHAR(255) NULL DEFAULT NULL AFTER `team`',
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.2 `status` — add it if missing, then normalize the ENUM order.
SET @has := (SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE()
               AND table_name = 'events'
               AND column_name = 'status');
SET @sql := IF(@has = 0,
    "ALTER TABLE `events` ADD COLUMN `status` ENUM('draft','published') NOT NULL DEFAULT 'draft' AFTER `filiere`",
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Sanitize before changing the type: an unexpected value (e.g. an empty
-- string left by a non-strict import) would otherwise abort the MODIFY.
UPDATE `events`
   SET `status` = 'draft'
 WHERE `status` IS NULL
    OR `status` NOT IN ('draft', 'published');

-- Normalizes a hand-made ENUM('published','draft') into the correct
-- ENUM('draft','published'). Safe to repeat.
ALTER TABLE `events`
    MODIFY `status` ENUM('draft','published') NOT NULL DEFAULT 'draft';

-- 2.3 `created_by` — the author column and its foreign key.
SET @has := (SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE()
               AND table_name = 'events'
               AND column_name = 'created_by');
SET @sql := IF(@has = 0,
    'ALTER TABLE `events` ADD COLUMN `created_by` BIGINT UNSIGNED NOT NULL AFTER `status`',
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.4 `published_at` — required for the "past published_at = published" rule.
SET @has := (SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE()
               AND table_name = 'events'
               AND column_name = 'published_at');
SET @sql := IF(@has = 0,
    'ALTER TABLE `events` ADD COLUMN `published_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_by`',
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- If the table already had rows when `created_by` was added, those rows
-- get the value 0, which is not a real user. The FK below would then
-- fail with "Cannot add or update a child row". Fix them first:
-- UPDATE `events`
--    SET `created_by` = (SELECT MIN(`id`) FROM `users`)
--  WHERE `created_by` NOT IN (SELECT `id` FROM `users`);

-- The index is created together with the FK, but add it on its own if
-- an older table has the column without the index.
SET @has := (SELECT COUNT(DISTINCT index_name) FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = 'events'
               AND index_name = 'events_created_by_foreign');
SET @sql := IF(@has = 0,
    'ALTER TABLE `events` ADD INDEX `events_created_by_foreign` (`created_by`)',
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has := (SELECT COUNT(*) FROM information_schema.table_constraints
             WHERE table_schema = DATABASE()
               AND table_name = 'events'
               AND constraint_name = 'events_created_by_foreign');
SET @sql := IF(@has = 0,
    'ALTER TABLE `events` ADD CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)',
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.5 Backfill legacy rows (same logic as the Laravel migrations).
-- Any event whose published_at is already in the past is official.
UPDATE `events`
   SET `status` = 'published'
 WHERE `published_at` IS NOT NULL
   AND `published_at` <= NOW()
   AND `status` <> 'published';

-- A published row must always carry a published_at.
UPDATE `events`
   SET `published_at` = COALESCE(`created_at`, NOW())
 WHERE `status` = 'published'
   AND `published_at` IS NULL;

-- Rows left with a NULL status (only possible if the column was added
-- nullable by hand) are treated as published, matching the migration.
UPDATE `events`
   SET `status` = 'published'
 WHERE `status` IS NULL;


-- ---------------------------------------------------------------------
-- 3. REPAIR `event_user` (RSVP / attendance pivot)
-- ---------------------------------------------------------------------
-- Run only if the pivot table is missing or malformed. Section 1 above
-- already creates it correctly when absent.

-- Missing pivot for a broken install:
-- CREATE TABLE IF NOT EXISTS `event_user` ( ... section 1 ... );

-- Missing `attended` flag:
SET @has := (SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE()
               AND table_name = 'event_user'
               AND column_name = 'attended');
SET @sql := IF(@has = 0,
    'ALTER TABLE `event_user` ADD COLUMN `attended` TINYINT(1) NOT NULL DEFAULT 0',
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Missing unique (event_id, user_id) — prevents double RSVPs:
SET @has := (SELECT COUNT(DISTINCT index_name) FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = 'event_user'
               AND index_name = 'event_user_event_id_user_id_unique');
SET @sql := IF(@has = 0,
    'ALTER TABLE `event_user` ADD UNIQUE KEY `event_user_event_id_user_id_unique` (`event_id`, `user_id`)',
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;


-- ---------------------------------------------------------------------
-- 4. LAST RESORT — full rebuild (DESTRUCTIVE, empty table only)
-- ---------------------------------------------------------------------
-- Only if `events` is structurally wrong beyond repair and you can
-- afford to lose its rows. Back up first:
--   mysqldump -u USER -p DB events event_user > events_backup.sql
--
-- DROP TABLE IF EXISTS `event_user`;
-- DROP TABLE IF EXISTS `events`;
-- then re-run section 1.
--
-- If the rows matter, export them first (SHOW CREATE TABLE + INSERTs)
-- and re-import after section 1.


-- ---------------------------------------------------------------------
-- 5. VERIFICATION
-- ---------------------------------------------------------------------
-- Expect the 14 columns of section 1, in that order.
-- SELECT column_name, column_type, is_nullable, column_default
-- FROM information_schema.columns
-- WHERE table_schema = DATABASE() AND table_name = 'events'
-- ORDER BY ordinal_position;

-- Expect: enum('draft','published') and default 'draft'.
-- SELECT column_type, column_default FROM information_schema.columns
-- WHERE table_schema = DATABASE()
--   AND table_name = 'events' AND column_name = 'status';

-- Expect 4 constraints: 1 PRIMARY + 1 FK per table, plus the unique key.
-- SELECT table_name, constraint_name, constraint_type
-- FROM information_schema.table_constraints
-- WHERE table_schema = DATABASE() AND table_name IN ('events','event_user');

-- Expect 1 FK from event_user.event_id -> events.id (ON DELETE CASCADE).
-- SELECT table_name, column_name, referenced_table_name, referenced_column_name
-- FROM information_schema.key_column_usage
-- WHERE table_schema = DATABASE()
--   AND referenced_table_name = 'events';

-- Smoke test: published events that are still upcoming (the landing page
-- query). A row with starts_at NULL is exactly what makes templates
-- render blank, so this must return 0.
-- SELECT COUNT(*) AS broken_rows FROM events WHERE starts_at IS NULL;
-- SELECT id, title, starts_at, status FROM events
-- WHERE status = 'published' AND starts_at >= NOW()
-- ORDER BY starts_at LIMIT 5;


-- ---------------------------------------------------------------------
-- 6. AFTER APPLYING THIS FILE
-- ---------------------------------------------------------------------
-- If you created the tables with this SQL instead of `php artisan
-- migrate`, mark the matching migrations as run so a later migrate does
-- not try to re-create them:
--
-- INSERT INTO migrations (migration, batch)
-- SELECT v.migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations)
-- FROM (
--     SELECT '2026_09_29_183717_create_events_table' AS migration
--     UNION ALL SELECT '2026_09_29_191915_create_event_user_table'
--     UNION ALL SELECT '2026_10_17_100000_add_status_to_events_table'
--     UNION ALL SELECT '2026_10_17_120000_add_filiere_to_events_table'
--     UNION ALL SELECT '2026_10_17_150000_fix_events_status_enum_and_backfill'
-- ) AS v
-- WHERE NOT EXISTS (SELECT 1 FROM migrations m WHERE m.migration = v.migration);
--
-- There are no seeders for events: the tables start empty by design.
-- =====================================================================
-- END OF FILE
-- =====================================================================
