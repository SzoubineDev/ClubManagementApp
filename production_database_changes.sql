-- =====================================================================
--  production_database_changes.sql
-- ---------------------------------------------------------------------
--  Club Management App — database changes made in the session that
--  ended in commit 80dc29a ("changes"), compared against its parent
--  bd35091 ("changin president photo").
--
--  Target engine : MySQL 8.x (also valid on MariaDB 10.x / 11.x)
--  Target schema : utf8mb4 / utf8mb4_unicode_ci  (matches dev DB)
--  Source of truth: actual dev schema captured with SHOW CREATE TABLE,
--                   not just the migration files.
--
--  WHAT IS IN THIS FILE
--    1. Five new Spatie roles (data only, existing `roles` table).
--    2. New table  attendance_records.
--    3. New table  videos  (already includes `library`).
--    4. Optional: the 200 seeded video-library rows.
--    5. Optional: the new permission names used by the new features.
--    6. Verification + rollback blocks.
--
--  WHAT IS *NOT* IN THIS FILE
--    - No existing table was altered structurally in this session.
--      `videos` was created and then widened with a `library` column
--      inside the same session; that is folded into its CREATE TABLE.
--    - No stored procedures, functions, or triggers were added
--      (verified: information_schema.routines / .triggers are empty).
--
--  IMPORTANT — PICK ONE DEPLOYMENT PATH
--    Path A (recommended): run this file with the MySQL client.
--      Then do NOT run `php artisan migrate` for the four 2026_11_15_*
--      migrations, or Laravel will try to re-create existing tables.
--      Either insert their rows into the `migrations` table (see the
--      bottom of this file) or run the migrations on a fresh database.
--    Path B: run `php artisan migrate` only. It is equivalent, but the
--      seeders (step 4) still have to be run separately.
--    Both paths together will fail — do not mix them.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 0. PRE-FLIGHT CHECKS (read-only, run these first)
-- ---------------------------------------------------------------------
-- These objects must already exist. Both target tables reference
-- `users`, so `users` has to be InnoDB with an indexed `id`.

-- SELECT VERSION() AS server_version;
-- SELECT @@character_set_database, @@collation_database;
-- SHOW TABLES LIKE 'users';
-- SHOW TABLES LIKE 'roles';
-- SHOW CREATE TABLE users;          -- expect InnoDB, PK(id)
-- SHOW CREATE TABLE roles;          -- expect roles_name_guard_name_unique

-- Guard: this file assumes `videos` and `attendance_records` do NOT
-- exist yet in production. If either already exists, skip that block.
-- SHOW TABLES LIKE 'videos';
-- SHOW TABLES LIKE 'attendance_records';


-- ---------------------------------------------------------------------
-- 1. NEW ROLES (data only — `roles` table already exists)
-- ---------------------------------------------------------------------
-- From database/migrations/2026_11_15_100000_create_club_management_roles.php
--
-- That migration only inserts rows; it creates no schema. It used
-- updateOrInsert(), so the SQL below is written to be idempotent too:
-- existing roles are left exactly as they are.
--
-- Roles were verified as present in the dev DB:
--   admin, president, member, section_chef, rh_manager,
--   media_manager, design_manager, assistant_general

INSERT INTO roles (name, guard_name, created_at, updated_at)
SELECT v.name, 'web', NOW(), NOW()
FROM (
    SELECT 'section_chef'      AS name
    UNION ALL SELECT 'rh_manager'
    UNION ALL SELECT 'media_manager'
    UNION ALL SELECT 'design_manager'
    UNION ALL SELECT 'assistant_general'
) AS v
WHERE NOT EXISTS (
    SELECT 1 FROM roles r
    WHERE r.name = v.name
      AND r.guard_name = 'web'
);
-- Expected: 5 rows inserted on a DB that only had
-- admin / president / member; 0 rows on a re-run.


-- ---------------------------------------------------------------------
-- 2. NEW TABLE: attendance_records
-- ---------------------------------------------------------------------
-- From database/migrations/2026_11_15_110000_create_attendance_records_table.php
-- Used by: app/Models/AttendanceRecord.php,
--          app/Http/Controllers/SectionAttendanceController.php

CREATE TABLE IF NOT EXISTS `attendance_records` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED NOT NULL,
    `date`        DATE            NOT NULL,
    `present`     TINYINT(1)      NOT NULL DEFAULT 0,
    `recorded_by` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at`  TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `attendance_records_user_id_date_unique` (`user_id`, `date`),
    KEY `attendance_records_recorded_by_foreign` (`recorded_by`),
    KEY `attendance_records_date_index` (`date`),
    CONSTRAINT `attendance_records_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `attendance_records_recorded_by_foreign`
        FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Column notes
--   id          surrogate PK, auto-increment.
--   user_id     the member whose presence is recorded.
--   date        one calendar day (DATE, no time).
--   present     boolean flag, default 0 = absent.
--   recorded_by whoever ticked the box (section chef or RH manager);
--               nullable because the recorder account may be deleted.
--   created_at / updated_at standard Laravel timestamps.
--
-- Keys
--   Unique (user_id, date) — one record per member per day; this is what
--   the controller's "mark present/absent" upsert relies on.
--   Index (date)           — fast "all presence for a given day".
--   Index (recorded_by)    — created automatically for the FK.
--
-- Relationship added (Eloquent side)
--   User        hasMany  AttendanceRecord   (user_id)
--   AttendanceRecord belongsTo User          (user_id)
--   AttendanceRecord belongsTo User as recorder (recorded_by)


-- ---------------------------------------------------------------------
-- 3. NEW TABLE: videos
-- ---------------------------------------------------------------------
-- From database/migrations/2026_11_15_120000_create_videos_table.php
--  + database/migrations/2026_11_15_130000_add_library_to_videos_table.php
--
-- In dev these ran as CREATE TABLE followed by ALTER TABLE ADD COLUMN
-- `library`. Production only needs the final shape below; the ALTER is
-- already folded in.
-- Used by: app/Models/Video.php, VideoController, DesignVideoController.

CREATE TABLE IF NOT EXISTS `videos` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`         VARCHAR(255)    NOT NULL,
    `description`   TEXT            NULL DEFAULT NULL,
    `youtube_url`   VARCHAR(255)    NOT NULL,
    `thumbnail_url` VARCHAR(255)    NULL DEFAULT NULL,
    `sort_order`    INT UNSIGNED    NOT NULL DEFAULT 0,
    `library`       VARCHAR(255)    NOT NULL DEFAULT 'media',
    `created_at`    TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `videos_library_index` (`library`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Column notes
--   title          card heading shown on the library page.
--   description    free text; the seeders store "Category · Channel".
--   youtube_url    full https://www.youtube.com/watch?v=... link.
--   thumbnail_url  optional custom thumbnail; the model derives the
--                  YouTube thumbnail when this is NULL.
--   sort_order     display/curation order.
--   library        which pool the row belongs to:
--                    'media'  -> media_manager only
--                    'design' -> design_manager only
--                  This is the column that isolates the two libraries.
--
-- Keys
--   Index (library) — every query filters on it
--   (Video::scopeInLibrary), so it is indexed deliberately.
--
-- No foreign keys: a video is not owned by a user row.
--
-- If `library` alone is missing on an existing production `videos`
-- table (i.e. the base table was already deployed earlier), apply just:
--
--   ALTER TABLE `videos`
--       ADD COLUMN `library` VARCHAR(255) NOT NULL DEFAULT 'media'
--       AFTER `sort_order`,
--       ADD INDEX `videos_library_index` (`library`);


-- ---------------------------------------------------------------------
-- 4. OPTIONAL — seeded video-library content (200 rows)
-- ---------------------------------------------------------------------
-- Source: database/seeders/VideoSeeder.php       (100 "media" rows)
--         database/seeders/DesignVideoSeeder.php (100 "design" rows)
-- Both are called by DatabaseSeeder.
--
-- These are content rows, not schema. Without them both library pages
-- render their empty state. Every statement is guarded with NOT EXISTS
-- on (youtube_url, library), so re-running is safe; `sort_order` is
-- 1..100 per library, matching the seeders' `$index + 1`.
--
-- To skip the content, delete everything from here down to section 5.

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The Power of Vulnerability | Brené Brown | TED', 'Self-improvement & motivation · TED', 'https://www.youtube.com/watch?v=iCvmsMzlF7o', NULL, 1, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=iCvmsMzlF7o'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The Power of Introverts | Susan Cain | TED', 'Self-improvement & motivation · TED', 'https://www.youtube.com/watch?v=c0KYU2j0TM4', NULL, 2, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=c0KYU2j0TM4'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'A Simple Way to Break a Bad Habit | Judson Brewer | TED', 'Self-improvement & motivation · TED', 'https://www.youtube.com/watch?v=-moW9jvvMr4', NULL, 3, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=-moW9jvvMr4'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to talk to the worst parts of yourself | Karen Faith | TEDxKC', 'Self-improvement & motivation · TEDx Talks', 'https://www.youtube.com/watch?v=gUV5DJb6KGs', NULL, 4, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=gUV5DJb6KGs'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'After watching this, your brain will not be the same | Lara Boyd | TEDxVancouver', 'Self-improvement & motivation · TEDx Talks', 'https://www.youtube.com/watch?v=LNHBMFCzznE', NULL, 5, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=LNHBMFCzznE'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Achieve Your Most Ambitious Goals | Stephen Duneier | TEDxTucson', 'Self-improvement & motivation · TEDx Talks', 'https://www.youtube.com/watch?v=TQMbvJNRpLE', NULL, 6, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=TQMbvJNRpLE'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How To Eliminate Self Doubt Forever & The Power of Your Unconscious Mind | Peter Sage', 'Self-improvement & motivation · TEDx Talks', 'https://www.youtube.com/watch?v=v1ojZKWfShQ', NULL, 7, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=v1ojZKWfShQ'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The Secrets and Science of Mental Toughness | Joe Risser', 'Self-improvement & motivation · TEDx Talks', 'https://www.youtube.com/watch?v=rNxC16mlO60', NULL, 8, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=rNxC16mlO60'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The secret to self control | Jonathan Bricker | TEDxRainier', 'Self-improvement & motivation · TEDx Talks', 'https://www.youtube.com/watch?v=tTb3d5cjSFI', NULL, 9, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=tTb3d5cjSFI'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The skill of self confidence | Dr. Ivan Joseph | TEDxRyersonU', 'Self-improvement & motivation · TEDx Talks', 'https://www.youtube.com/watch?v=w-HYZv6HzAs', NULL, 10, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=w-HYZv6HzAs'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How Long It Takes To Change Your Life? | Nwal Hadaki | TEDxSafirSchool', 'Self-improvement & motivation · TEDx Talks', 'https://www.youtube.com/watch?v=g85WsxE1gAU', NULL, 11, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=g85WsxE1gAU'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How not to take things personally? | Frederik Imbo | TEDxMechelen', 'Self-improvement & motivation · TEDx Talks', 'https://www.youtube.com/watch?v=LnJwH_PZXnM', NULL, 12, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=LnJwH_PZXnM'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'What Makes a Good Life? Lessons from the Longest Study on Happiness | Robert Waldinger | TED', 'Self-improvement & motivation · TED', 'https://www.youtube.com/watch?v=8KkKuTCFvzI', NULL, 13, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=8KkKuTCFvzI'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Who are you, really? The puzzle of personality | Brian Little | TED', 'Self-improvement & motivation · TED', 'https://www.youtube.com/watch?v=qYvXk_bqlBk', NULL, 14, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=qYvXk_bqlBk'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to overcome your mistakes', 'Self-improvement & motivation · TED-Ed', 'https://www.youtube.com/watch?v=eBz7iUJu9UM', NULL, 15, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=eBz7iUJu9UM'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Is it normal to talk to yourself?', 'Self-improvement & motivation · TED-Ed', 'https://www.youtube.com/watch?v=iNyUmbmQQZg', NULL, 16, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=iNyUmbmQQZg'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'WATCH THIS EVERYDAY AND CHANGE YOUR LIFE - Denzel Washington Motivational Speech', 'Motivation · AlexKaltsMotivation', 'https://www.youtube.com/watch?v=tbnzAVRZ9Xc', NULL, 17, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=tbnzAVRZ9Xc'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'KILL YOUR EXCUSES - Motivational Speech', 'Motivation · Ben Lionel Scott', 'https://www.youtube.com/watch?v=JjvN_hYDp3g', NULL, 18, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=JjvN_hYDp3g'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'WHEN LIFE BREAKS YOU - Powerful Motivational Speech', 'Motivation · Ben Lionel Scott', 'https://www.youtube.com/watch?v=0cwtNOq_k4w', NULL, 19, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=0cwtNOq_k4w'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Admiral McRaven Leaves the Audience SPEECHLESS', 'Motivation · MotivationHub', 'https://www.youtube.com/watch?v=TBuIGBCF9jc', NULL, 20, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=TBuIGBCF9jc'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'NO EXCUSES - Best Motivational Video', 'Motivation · Ben Lionel Scott', 'https://www.youtube.com/watch?v=wnHW6o8WMas', NULL, 21, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=wnHW6o8WMas'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'WHEN IT HURTS - Powerful Motivational Speech', 'Motivation · Ben Lionel Scott', 'https://www.youtube.com/watch?v=zzfREEPbUsA', NULL, 22, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=zzfREEPbUsA'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'IT''S TIME TO PUSH YOURSELF AND MAKE IT HAPPEN', 'Motivation · Absolute Motivation', 'https://www.youtube.com/watch?v=tEt-Gsd_2S8', NULL, 23, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=tEt-Gsd_2S8'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How Great Leaders Inspire Action | Simon Sinek', 'Self-improvement & motivation · TED', 'https://www.youtube.com/watch?v=qp0HIF3SfI4', NULL, 24, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=qp0HIF3SfI4'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Barack Obama''s Inspirational Speech with Subtitles', 'Motivation · English Motivational Videos', 'https://www.youtube.com/watch?v=PGAUdWfB8nLg', NULL, 25, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=PGAUdWfB8nLg'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The power of believing that you can improve | Carol Dweck | TED', 'Growth mindset · TED', 'https://www.youtube.com/watch?v=_X0mgOOSpLU', NULL, 26, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=_X0mgOOSpLU'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Grit: The Power of Passion and Perseverance | Angela Lee Duckworth | TED', 'Growth mindset · TED', 'https://www.youtube.com/watch?v=H14bBuluwB8', NULL, 27, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=H14bBuluwB8'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Developing a Growth Mindset with Carol Dweck', 'Growth mindset · Stanford Alumni', 'https://www.youtube.com/watch?v=hiiEeMN7vbQ', NULL, 28, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=hiiEeMN7vbQ'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The Mindset of a Champion | Carson Byblow | TEDxYouth@AASSofia', 'Growth mindset · TEDx Talks', 'https://www.youtube.com/watch?v=px9CzSZsa0Y', NULL, 29, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=px9CzSZsa0Y'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The Power of belief -- mindset and success | Eduardo Briceno | TEDxManhattanBeach', 'Growth mindset · TEDx Talks', 'https://www.youtube.com/watch?v=pN34FNbOKXc', NULL, 30, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=pN34FNbOKXc'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Change your mindset, change the game | Dr. Alia Crum | TEDxTraverseCity', 'Growth mindset · TEDx Talks', 'https://www.youtube.com/watch?v=0tqq66zwa7g', NULL, 31, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=0tqq66zwa7g'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Growth Mindset vs. Fixed Mindset', 'Growth mindset · Sprouts', 'https://www.youtube.com/watch?v=KUWn_TJTrnU', NULL, 32, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=KUWn_TJTrnU'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Maximize Productivity, Physical & Mental Health With Daily Tools', 'Productivity · Andrew Huberman', 'https://www.youtube.com/watch?v=Pmd6knanPKw', NULL, 33, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Pmd6knanPKw'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Be So Productive it Feels ILLEGAL', 'Productivity · Dan Martell', 'https://www.youtube.com/watch?v=hSGt_rhu49U', NULL, 34, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=hSGt_rhu49U'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '30 Habits That (Quietly) Changed My Life Forever', 'Productivity · RelentX', 'https://www.youtube.com/watch?v=9E2SdLD3Kpc', NULL, 35, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=9E2SdLD3Kpc'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '17 Micro-Habits to Improve Your Focus', 'Productivity · Ali Abdaal', 'https://www.youtube.com/watch?v=7EWYjFqgmHM', NULL, 36, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=7EWYjFqgmHM'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '5 Essential Tips for Long-Lasting Productivity', 'Productivity · Ali Abdaal', 'https://www.youtube.com/watch?v=FbSNfj2S6Pw', NULL, 37, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=FbSNfj2S6Pw'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Boring Everyday Habits That Completely Transform Your Life', 'Productivity · EverythingProfessor', 'https://www.youtube.com/watch?v=pw1b43X3u-s', NULL, 38, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=pw1b43X3u-s'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How To Defuse Procrastination And Be Productive (Without Using Willpower)', 'Productivity · HealthyGamerGG', 'https://www.youtube.com/watch?v=VBifDZwPiI4', NULL, 39, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=VBifDZwPiI4'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The Perfect Morning Habits (Backed by Science)', 'Productivity · The Inspire Path', 'https://www.youtube.com/watch?v=uVzCPllwmqw', NULL, 40, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=uVzCPllwmqw'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '6 ways to boost your creativity: Pixar Brainstorming Techniques', 'Brainstorming · Find Your Voice', 'https://www.youtube.com/watch?v=kPnDkEwJmng', NULL, 41, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=kPnDkEwJmng'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to generate the most Creative Ideas (even if you are not creative)', 'Creativity · Nextcore', 'https://www.youtube.com/watch?v=UZ_peeloxAA', NULL, 42, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=UZ_peeloxAA'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Six Creative Ways To Brainstorm Ideas', 'Brainstorming · Investis Digital', 'https://www.youtube.com/watch?v=yAidvTKX6xM', NULL, 43, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=yAidvTKX6xM'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Brainstorming Techniques: How to Innovate in Groups', 'Brainstorming · Sprouts', 'https://www.youtube.com/watch?v=YXZamW4-Ysk', NULL, 44, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=YXZamW4-Ysk'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'IDEO Brainstorming Video from IDEO U', 'Brainstorming · Hackers Design Book', 'https://www.youtube.com/watch?v=VvdJzeO9yN8', NULL, 45, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=VvdJzeO9yN8'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The secret to better brainstorming | Dr. Roger Firestien | TEDxOmaha', 'Brainstorming · TEDx Talks', 'https://www.youtube.com/watch?v=e4h53SRClNw', NULL, 46, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=e4h53SRClNw'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How I Run Brainstorming Sessions (UX Framework)', 'Brainstorming · Chris from rectangles', 'https://www.youtube.com/watch?v=9mOIgNml-eg', NULL, 47, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=9mOIgNml-eg'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Generate Ideas with the SCAMPER Technique', 'Creativity · Michael Masters', 'https://www.youtube.com/watch?v=-OMJINQIZzA', NULL, 48, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=-OMJINQIZzA'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '5 ways to create more ideas - brainstorming techniques', 'Brainstorming · Ron White Memory Expert', 'https://www.youtube.com/watch?v=8WHm9jzBWAw', NULL, 49, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=8WHm9jzBWAw'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '4 simple ways to have a great idea | Richard St. John', 'Creativity · TED Archive', 'https://www.youtube.com/watch?v=mtn31hh6kU4', NULL, 50, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=mtn31hh6kU4'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '6-3-5 Brainwriting Method | Brainstorming Techniques', 'Brainstorming · Ed Tchoi', 'https://www.youtube.com/watch?v=zbj0C5aqE4E', NULL, 51, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=zbj0C5aqE4E'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'A Brainstorming Technique for Students that ACTUALLY Works', 'Brainstorming · John Spencer', 'https://www.youtube.com/watch?v=GLpZ6RZHyoM', NULL, 52, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=GLpZ6RZHyoM'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Creative thinking - how to get out of the box and generate ideas | Giovanni Corazza', 'Creativity · TEDx Talks', 'https://www.youtube.com/watch?v=bEusrD8g-dM', NULL, 53, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=bEusrD8g-dM'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '7 Rules for the Perfect Brainstorming Session', 'Brainstorming · IDEO U', 'https://www.youtube.com/watch?v=2B7aCIuEDxk', NULL, 54, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=2B7aCIuEDxk'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Do Schools Kill Creativity? | Sir Ken Robinson | TED', 'Creativity · TED', 'https://www.youtube.com/watch?v=iG9CE55wbtY', NULL, 55, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=iG9CE55wbtY'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Speak So That People Want to Listen | Julian Treasure | TED', 'Public speaking · TED', 'https://www.youtube.com/watch?v=eIho2S0ZahI', NULL, 56, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=eIho2S0ZahI'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Your Body Language May Shape Who You Are | Amy Cuddy | TED', 'Public speaking · TED', 'https://www.youtube.com/watch?v=Ks-_Mh1QhMc', NULL, 57, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Ks-_Mh1QhMc'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The Danger of a Single Story | Chimamanda Ngozi Adichie | TED', 'Public speaking · TED', 'https://www.youtube.com/watch?v=D9Ihs241zeg', NULL, 58, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=D9Ihs241zeg'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'TED''s secret to great public speaking | Chris Anderson | TED', 'Public speaking · TED', 'https://www.youtube.com/watch?v=-FOCpMAww28', NULL, 59, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=-FOCpMAww28'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to debate in English: Vocabulary and Phrases', 'Debate · ABA English', 'https://www.youtube.com/watch?v=-VVFwNo_29I', NULL, 60, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=-VVFwNo_29I'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'NEVER Feel Anxiety Again When Giving A Speech (5 EASY Methods)', 'Public speaking · Vinh Giang', 'https://www.youtube.com/watch?v=Edg54WnYqkE', NULL, 61, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Edg54WnYqkE'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The 3-2-1 Speaking Trick That Forces You To Stop Rambling!', 'Public speaking · Vinh Giang', 'https://www.youtube.com/watch?v=5m-C5mwpmxU', NULL, 62, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=5m-C5mwpmxU'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Barack Obama''s top 4 speaking techniques (no B.S.)', 'Public speaking · Ultraspeaking', 'https://www.youtube.com/watch?v=RWpeq4xPcaI', NULL, 63, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=RWpeq4xPcaI'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Train for any argument with Harvard''s former debate coach | Bo Seo', 'Debate · Big Think', 'https://www.youtube.com/watch?v=_WjUFuW2J0A', NULL, 64, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=_WjUFuW2J0A'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Debate world champion explains how to argue | Bo Seo', 'Debate · Big Think', 'https://www.youtube.com/watch?v=2pVdSEp-tT8', NULL, 65, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=2pVdSEp-tT8'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Improve Your English Speaking Skills | Practice English Listening, Vocabulary and Shadowing', 'English learning · English Easy Practice', 'https://www.youtube.com/watch?v=ySyPIeBxYBo', NULL, 66, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=ySyPIeBxYBo'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Speak With Me: 2 Hour English Speaking Practice', 'English learning · Speak English With Vanessa', 'https://www.youtube.com/watch?v=topBLaz4zgk', NULL, 67, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=topBLaz4zgk'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Improve Your English Speaking Skills | Shadowing English Practice', 'English learning · Confident English with Kirsty', 'https://www.youtube.com/watch?v=YGwlBRg4bvY', NULL, 68, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=YGwlBRg4bvY'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Best Way to Practice Speaking English: Shadowing English Conversation', 'English learning · English Easy Practice', 'https://www.youtube.com/watch?v=E_70wtDChh0', NULL, 69, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=E_70wtDChh0'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Repeat-After-Me Speaking Practice with a fun little story', 'English learning · English Coach Chad', 'https://www.youtube.com/watch?v=6E7i_7Mr_5c', NULL, 70, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=6E7i_7Mr_5c'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Simple English Conversations | Real-Life English for Beginners', 'English learning · Proactive English Practice', 'https://www.youtube.com/watch?v=kF9wC2I2Suc', NULL, 71, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=kF9wC2I2Suc'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Learn Daily English Conversations in 30 Minutes | Practice English Speaking and Listening', 'English learning · Learn English with Jessica', 'https://www.youtube.com/watch?v=pKYQR9npunU', NULL, 72, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=pKYQR9npunU'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Improve your Speaking and Conversational skills with me', 'English learning · English Fluency Journey', 'https://www.youtube.com/watch?v=aRBzYEn7dhM', NULL, 73, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=aRBzYEn7dhM'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'THINK AND SPEAK IN ENGLISH | How To Talk About Your Daily Life Fluently In English', 'English learning · Speak English With Tiffani', 'https://www.youtube.com/watch?v=ag3RnEaB3zM', NULL, 74, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=ag3RnEaB3zM'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '30 Minutes of Real English Conversation You''ll Actually Use (A2-B2)', 'English learning · Speak English with David & Alice', 'https://www.youtube.com/watch?v=v07kPyPkLYw', NULL, 75, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=v07kPyPkLYw'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Simple Daily English Conversations | Listening and Speaking Practice for Beginners', 'English learning · Proactive English Practice', 'https://www.youtube.com/watch?v=sHFse8a8uik', NULL, 76, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=sHFse8a8uik'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Shadowing English Speaking Practice | Speak Fluently 5-Day English Speaking Challenge', 'English learning · Speak English with David & Alice', 'https://www.youtube.com/watch?v=k0aLziRxyG8', NULL, 77, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=k0aLziRxyG8'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Improve your English Grammar in One Hour | Basic English Grammar', 'English grammar · linguamarina', 'https://www.youtube.com/watch?v=QXVzmzhxWWc', NULL, 78, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=QXVzmzhxWWc'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Learn ALL 16 TENSES Easily in under 30 Minutes', 'English grammar · English with Lucy', 'https://www.youtube.com/watch?v=Ljjiw9mC_Cg', NULL, 79, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Ljjiw9mC_Cg'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The 8 Parts of Speech in English Grammar (+ Free PDF & Quiz)', 'English grammar · English with Lucy', 'https://www.youtube.com/watch?v=chjmnCSPnbw', NULL, 80, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=chjmnCSPnbw'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '15 Real-Life English Conversations for Beginners | Easy English Speaking Practice', 'English learning · Easy English Practice', 'https://www.youtube.com/watch?v=54xQ2CBCwrE', NULL, 81, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=54xQ2CBCwrE'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Advanced speaking practice: English Imitation Lesson', 'English learning · mmmEnglish', 'https://www.youtube.com/watch?v=FfhZFRvmaVY', NULL, 82, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=FfhZFRvmaVY'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '6 Useful English Words You Should Know | Everyday English Vocabulary', 'English learning · Learn English Today', 'https://www.youtube.com/watch?v=GmdcD0QPiPk', NULL, 83, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=GmdcD0QPiPk'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '1 Hour English Vocabulary Course: Learn 250+ Important English Vocabulary Words', 'English learning · English with Lucy', 'https://www.youtube.com/watch?v=F30R0tDIXP0', NULL, 84, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=F30R0tDIXP0'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '600 English Words for Everyday Life - Basic Vocabulary #30', 'English learning · Learn English with EnglishClass101.com', 'https://www.youtube.com/watch?v=mgty3Bgu-YY', NULL, 85, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=mgty3Bgu-YY'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '1000 English Words For Daily Speech | English Vocabulary Masterclass', 'English learning · JForrest English', 'https://www.youtube.com/watch?v=g50sjKXugwo', NULL, 86, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=g50sjKXugwo'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'I use these words every day: English Vocabulary Lesson', 'English learning · Speak English With Vanessa', 'https://www.youtube.com/watch?v=FatDtR1lkKk', NULL, 87, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=FatDtR1lkKk'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How To Learn 100 English Vocabulary Words in 5 Days', 'English learning · Speak English With Tiffani', 'https://www.youtube.com/watch?v=EK8xd8fNV1U', NULL, 88, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=EK8xd8fNV1U'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Remember English Vocabulary', 'English learning · Learn English with Bob the Canadian', 'https://www.youtube.com/watch?v=ZM6Sg4SN7kU', NULL, 89, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=ZM6Sg4SN7kU'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '3 Things You Need to NEVER Forget Vocabulary Again', 'English learning · English Speaking Success', 'https://www.youtube.com/watch?v=5Y5TktzkeTI', NULL, 90, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=5Y5TktzkeTI'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'B2 English Vocab That Native Speakers Use Every Day', 'English learning · Interactive English', 'https://www.youtube.com/watch?v=ieCV6C-Qe28', NULL, 91, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=ieCV6C-Qe28'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Advanced Vocabulary in 60 Minutes (Precise words you need to know!)', 'English learning · Interactive English', 'https://www.youtube.com/watch?v=uLN6IdRtDhg', NULL, 92, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=uLN6IdRtDhg'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Learn English Speaking Practice for Beginners | English Conversation', 'English learning · Best English Online', 'https://www.youtube.com/watch?v=hx36h6bVXZ0', NULL, 93, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=hx36h6bVXZ0'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Learn English Conversation - Oxford English Daily Conversation Part 1', 'English learning · Learn English with English7Levels', 'https://www.youtube.com/watch?v=DPmtnb8NBog', NULL, 94, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=DPmtnb8NBog'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Everyday English Conversation Practice | 30 Minutes English Listening', 'English learning · English Easy Practice', 'https://www.youtube.com/watch?v=henIVlCPVIY', NULL, 95, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=henIVlCPVIY'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '1.5 HOUR English Conversation Lesson', 'English learning · Speak English With Vanessa', 'https://www.youtube.com/watch?v=lhFU5H5KPFE', NULL, 96, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=lhFU5H5KPFE'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '50 Simple English Conversations for Beginners: Real English Dialogues (A1-A2)', 'English learning · Enjoy English Practice', 'https://www.youtube.com/watch?v=XSxZGrBn0Vo', NULL, 97, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=XSxZGrBn0Vo'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Build your vocabulary fast: Live English Class', 'English learning · BBC Learning English', 'https://www.youtube.com/watch?v=Mh6RLmlUtgo', NULL, 98, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Mh6RLmlUtgo'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How can I speak English more fluently? Ask BBC Learning English', 'English learning · BBC Learning English', 'https://www.youtube.com/watch?v=LDkvRFCm8No', NULL, 99, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=LDkvRFCm8No'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to introduce yourself: Easy English Conversations Episode 1', 'English learning · BBC Learning English', 'https://www.youtube.com/watch?v=I_tRSrPru94', NULL, 100, 'media', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=I_tRSrPru94'
      AND library = 'media'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Tutorial for Beginners (2026) – Full Video Editing Guide', 'CapCut basics · Metics Media', 'https://www.youtube.com/watch?v=j5_471mO14c', NULL, 1, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=j5_471mO14c'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Beginner Tutorial (2026) - Beginner to Pro', 'CapCut basics · Matt Loui', 'https://www.youtube.com/watch?v=Zv9u0aLkfK0', NULL, 2, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Zv9u0aLkfK0'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Learn CapCut in 10 Minutes - Beginner Tutorial (PC & MAC)', 'CapCut basics · Gavin Herman', 'https://www.youtube.com/watch?v=ImKkcOE3y70', NULL, 3, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=ImKkcOE3y70'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Tutorial for Beginners', 'CapCut basics · ClipCookie', 'https://www.youtube.com/watch?v=qfHX2cNA4MY', NULL, 4, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=qfHX2cNA4MY'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Tutorial for Beginners 2026', 'CapCut basics · Metics Media', 'https://www.youtube.com/watch?v=EMDrgqepVhM', NULL, 5, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=EMDrgqepVhM'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut App Video Editing Tutorial - For Beginners', 'CapCut basics · Dee Nimmin', 'https://www.youtube.com/watch?v=P51CqlPOE_w', NULL, 6, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=P51CqlPOE_w'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Video Editing Tutorial for Beginners (2026) | Full Course', 'CapCut basics · Lerin Nic', 'https://www.youtube.com/watch?v=68KNzsmBarM', NULL, 7, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=68KNzsmBarM'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'In Depth CapCut Mobile Video Editing Masterclass', 'CapCut basics · Content Creators', 'https://www.youtube.com/watch?v=nEwHL9GRuFk', NULL, 8, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=nEwHL9GRuFk'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'In Depth CapCut Video Editing Tutorial - Mac & PC', 'CapCut basics · Content Creators', 'https://www.youtube.com/watch?v=gERszKkxZOw', NULL, 9, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=gERszKkxZOw'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Learn CapCut in 15 Minutes - Complete Mobile Video Editing Tutorial', 'CapCut basics · Stewart and Alina', 'https://www.youtube.com/watch?v=924il_WauMk', NULL, 10, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=924il_WauMk'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Learn CapCut in 15 Minutes (Beginner Tutorial)', 'CapCut basics · Joey Edits', 'https://www.youtube.com/watch?v=emp1iL8TRJg', NULL, 11, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=emp1iL8TRJg'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Beginners Tutorial: Edit for Free on Mac & PC', 'CapCut basics · Ben Claremont', 'https://www.youtube.com/watch?v=pHMoPh6mzm4', NULL, 12, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=pHMoPh6mzm4'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'You Won''t Use CapCut Transitions Again, After This Video!', 'Transitions · Davinci', 'https://www.youtube.com/watch?v=qSmyogAJudM', NULL, 13, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=qSmyogAJudM'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Make a Smooth Transition in CapCut - Easy Tutorial', 'Transitions · THE APP EXPERTS', 'https://www.youtube.com/watch?v=d0UVlG-cQmg', NULL, 14, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=d0UVlG-cQmg'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut: 3 Smooth Transitions Tutorial - Easy Method', 'Transitions · SiLeX EDITZ', 'https://www.youtube.com/watch?v=S9zIQqBQW94', NULL, 15, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=S9zIQqBQW94'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Top 5 Cinematic Transitions in CapCut PC', 'Transitions · Joey Edits', 'https://www.youtube.com/watch?v=CrqKi3gTouA', NULL, 16, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=CrqKi3gTouA'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Top 10 Smooth & Seamless Transitions in CapCut PC', 'Transitions · Joey Edits', 'https://www.youtube.com/watch?v=NtqmdaTzHN4', NULL, 17, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=NtqmdaTzHN4'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Do Transitions on CapCut Mobile - Full Guide', 'Transitions · BeamNG Terms TV', 'https://www.youtube.com/watch?v=Rqpnfu31FEo', NULL, 18, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Rqpnfu31FEo'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Trending Smooth Transition Tutorial', 'Transitions · Zettai Edits', 'https://www.youtube.com/watch?v=WJf4dCHAMDI', NULL, 19, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=WJf4dCHAMDI'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Easy Transitions Without Using Pro | CapCut Edit Tutorial', 'Transitions · VENOM BLINK', 'https://www.youtube.com/watch?v=xKAfWs6Uy3U', NULL, 20, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=xKAfWs6Uy3U'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Trending Transition Tutorial on CapCut', 'Transitions · One4Eleven', 'https://www.youtube.com/watch?v=iHeizt3CT0o', NULL, 21, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=iHeizt3CT0o'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Slide Transition Tutorial', 'Transitions · Way to Editz', 'https://www.youtube.com/watch?v=CNWTF-6R50M', NULL, 22, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=CNWTF-6R50M'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Mask Transition - CapCut PC Tutorial', 'Transitions · Faizan Amjed Official', 'https://www.youtube.com/watch?v=pC9TYHhXzgA', NULL, 23, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=pC9TYHhXzgA'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Smooth Slide Transition Tutorial - No Pro', 'Transitions · celestialeclipse', 'https://www.youtube.com/watch?v=ZArbr8KEggI', NULL, 24, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=ZArbr8KEggI'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Transition Ideas for You | CapCut Tutorial', 'Transitions · YourMina', 'https://www.youtube.com/watch?v=u9rr3oCZdgg', NULL, 25, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=u9rr3oCZdgg'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Cool AMV Transitions - Full Tutorial', 'Transitions · Editiverse', 'https://www.youtube.com/watch?v=m3AWKeaXHGQ', NULL, 26, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=m3AWKeaXHGQ'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '21 Best CapCut Tricks to Take You from a Noob to Pro', 'Editing tricks · Matt Loui', 'https://www.youtube.com/watch?v=pkyDzEHpZao', NULL, 27, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=pkyDzEHpZao'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '10 CapCut Editing Hacks That Will Blow Your Mind', 'Editing tricks · Modern Millie', 'https://www.youtube.com/watch?v=I49YEojjtf8', NULL, 28, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=I49YEojjtf8'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Best CapCut Video Editing Tips and Tricks', 'Editing tricks · Kevin Stratvert', 'https://www.youtube.com/watch?v=ol7PQ_arWPg', NULL, 29, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=ol7PQ_arWPg'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '4 Editing Tricks Famous Editors Use - But Never Teach! (CapCut Tutorial)', 'Editing tricks · Vaze9h', 'https://www.youtube.com/watch?v=HgE4SDjg6Qc', NULL, 30, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=HgE4SDjg6Qc'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '7 Stupidly Simple CapCut Tricks You Need to Know', 'Editing tricks · Zeven 7', 'https://www.youtube.com/watch?v=odxTRtU0HH8', NULL, 31, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=odxTRtU0HH8'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '10 Insane CapCut Editing Tricks', 'Editing tricks · Trevor Jones', 'https://www.youtube.com/watch?v=LpPZlf5s31E', NULL, 32, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=LpPZlf5s31E'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '8 Sneaky YouTube Editing Techniques and How to Do Them in CapCut', 'Editing tricks · Matt Loui', 'https://www.youtube.com/watch?v=YUvG0hzwTsg', NULL, 33, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=YUvG0hzwTsg'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '15 Pro Editing Techniques You Can Do in Free CapCut', 'Editing tricks · Matt Loui', 'https://www.youtube.com/watch?v=m-pKiIE2UH4', NULL, 34, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=m-pKiIE2UH4'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Insane CapCut Editing Tricks You Need to Master', 'Editing tricks · Matt Loui', 'https://www.youtube.com/watch?v=DDLlvJFTNw4', NULL, 35, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=DDLlvJFTNw4'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '10 CapCut Tricks That Look Hard and Aren''t', 'Editing tricks · Matt Loui', 'https://www.youtube.com/watch?v=chjI9UVl6p8', NULL, 36, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=chjI9UVl6p8'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '13 Mindblowing Video Editing Tips (With CapCut on Desktop)', 'Editing tricks · FILM CRUX', 'https://www.youtube.com/watch?v=gnWHHxr6TVs', NULL, 37, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=gnWHHxr6TVs'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Is Hard Until You Learn These 10 Tricks', 'Editing tricks · Matt Loui', 'https://www.youtube.com/watch?v=SxFYxB5EhGo', NULL, 38, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=SxFYxB5EhGo'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Advanced CapCut Tutorial: How to Make Pro Level Edits', 'Editing tricks · Collin Michael', 'https://www.youtube.com/watch?v=TnnQRfQafVA', NULL, 39, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=TnnQRfQafVA'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '5 Effects in CapCut That Instantly Upgrade Your Edits', 'Editing tricks · Zeven 7', 'https://www.youtube.com/watch?v=-LdTocMoppA', NULL, 40, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=-LdTocMoppA'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '15 Crazy CapCut Tricks to Go from Noob to Pro', 'Editing tricks · Matt Loui', 'https://www.youtube.com/watch?v=R9BNOSurmM0', NULL, 41, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=R9BNOSurmM0'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '10 CapCut Tricks Every Pro Editor Uses (But You Don''t)', 'Editing tricks · Patrik Key', 'https://www.youtube.com/watch?v=Ri9gUz9iNto', NULL, 42, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Ri9gUz9iNto'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '20 CapCut Mobile Effects to Completely Change Your Editing', 'Editing tricks · Matt Loui', 'https://www.youtube.com/watch?v=KVws1GVHoA0', NULL, 43, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=KVws1GVHoA0'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '5 Creative CapCut Edits You Need to Try', 'Editing tricks · Luka B', 'https://www.youtube.com/watch?v=VXDstEJSFeA', NULL, 44, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=VXDstEJSFeA'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Auto-Sync Your Video to the Music Beat in CapCut', 'Beat sync · AIVisualBlend', 'https://www.youtube.com/watch?v=LKeyhTQMFoE', NULL, 45, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=LKeyhTQMFoE'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Beat Tracking Tutorial | Sync Clips to Music', 'Beat sync · Vid & Views', 'https://www.youtube.com/watch?v=glY9XA8Zbio', NULL, 46, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=glY9XA8Zbio'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Use CapCut Beat Sync on Phone - Step by Step', 'Beat sync · TechGuidePro', 'https://www.youtube.com/watch?v=VfPSA6t8FRY', NULL, 47, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=VfPSA6t8FRY'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Auto Sync Beats in CapCut (Automatic Beat Editing)', 'Beat sync · Xpertise Logs', 'https://www.youtube.com/watch?v=eHYWEwuIW2Y', NULL, 48, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=eHYWEwuIW2Y'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Beat Sync Tutorial - Auto Beat Sync on PC', 'Beat sync · Graphic Guru', 'https://www.youtube.com/watch?v=KhKHX3dXzWA', NULL, 49, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=KhKHX3dXzWA'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Hit Beats in CapCut PC | Perfect Beat Sync Tutorial', 'Beat sync · Xpertise Logs', 'https://www.youtube.com/watch?v=y1x4we1T52s', NULL, 50, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=y1x4we1T52s'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Beat Sync Tutorial | Manual Beats + Auto Beats Explained', 'Beat sync · MOTION RAJ FX', 'https://www.youtube.com/watch?v=ZCYBTcyOGAA', NULL, 51, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=ZCYBTcyOGAA'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Best CapCut Tutorial for Syncing and Velocity', 'Beat sync · Boltzys', 'https://www.youtube.com/watch?v=rW77XjQVUiM', NULL, 52, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=rW77XjQVUiM'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Auto-Sync Video to Music Beat in CapCut', 'Beat sync · GPSOLVED', 'https://www.youtube.com/watch?v=GNnPcP9glI4', NULL, 53, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=GNnPcP9glI4'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Auto Beat Sync Transitions (Full Tutorial)', 'Beat sync · Graphic Guru', 'https://www.youtube.com/watch?v=Z91XqK1ts_k', NULL, 54, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Z91XqK1ts_k'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Perfect Beat Sync and Smooth Velocity on Mobile Using CapCut', 'Beat sync · THeOnE YT', 'https://www.youtube.com/watch?v=nApQplWQXrk', NULL, 55, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=nApQplWQXrk'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Make a Beat Sync Effect in CapCut', 'Beat sync · MJ Cinematics', 'https://www.youtube.com/watch?v=q-4CYD8RUj0', NULL, 56, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=q-4CYD8RUj0'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Master Sound Effects in CapCut: Step-by-Step Guide', 'Sound design · InstantTutorials', 'https://www.youtube.com/watch?v=853KtBoJnG4', NULL, 57, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=853KtBoJnG4'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Make Your Videos 10x Better with CapCut Sound Effects', 'Sound design · Harry Ortega', 'https://www.youtube.com/watch?v=uG5CP9FtyCM', NULL, 58, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=uG5CP9FtyCM'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '4 CapCut Sensory Sound Design Techniques', 'Sound design · Matt Loui', 'https://www.youtube.com/watch?v=eDuEd5TMjSQ', NULL, 59, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=eDuEd5TMjSQ'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How I Sound Design My Video in CapCut (Secret Sauce)', 'Sound design · byl', 'https://www.youtube.com/watch?v=05RfJDgJ4Pw', NULL, 60, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=05RfJDgJ4Pw'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Audio Editing for Beginners - Everything You Need to Know', 'Sound design · Vince Opra', 'https://www.youtube.com/watch?v=ZDqfifJ8nZw', NULL, 61, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=ZDqfifJ8nZw'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Make Your CapCut Edits Sound Professional', 'Sound design · Joey Edits', 'https://www.youtube.com/watch?v=7jwbnsckq6M', NULL, 62, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=7jwbnsckq6M'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Sound Editing Tutorial: Music, Voice-Overs & SFX Explained', 'Sound design · Miracamp', 'https://www.youtube.com/watch?v=DFjfHMnAlsY', NULL, 63, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=DFjfHMnAlsY'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Master Smooth Song Transitions in CapCut', 'Sound design · Knowfinity', 'https://www.youtube.com/watch?v=KpzkG1mRFgU', NULL, 64, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=KpzkG1mRFgU'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Use Sound Effects in CapCut (Complete Guide)', 'Sound design · Joey Edits', 'https://www.youtube.com/watch?v=zOTmSUlOEO0', NULL, 65, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=zOTmSUlOEO0'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '20 Must-Have CapCut Sound Effects for Your Next Video', 'Sound design · Creative Suite Tutorials', 'https://www.youtube.com/watch?v=5SBB725PiqI', NULL, 66, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=5SBB725PiqI'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Add Sound Effects in CapCut PC', 'Sound design · Guidie', 'https://www.youtube.com/watch?v=PnT2Xj-gv94', NULL, 67, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=PnT2Xj-gv94'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Extract Sound from a Video in CapCut', 'Sound design · Babis Gabe', 'https://www.youtube.com/watch?v=wdVxTBneIVY', NULL, 68, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=wdVxTBneIVY'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '5 Sound FX You Need in CapCut', 'Sound design · RSquared Edits', 'https://www.youtube.com/watch?v=IyHO0qEBy9E', NULL, 69, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=IyHO0qEBy9E'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Top 10 Favorite Sound Effects in CapCut', 'Sound design · Claire - UGC Creator', 'https://www.youtube.com/watch?v=EUH5luBMUDo', NULL, 70, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=EUH5luBMUDo'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Master Smooth Text Animations in CapCut', 'Text animation · Zeven 7', 'https://www.youtube.com/watch?v=FBhiUxlE2Sw', NULL, 71, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=FBhiUxlE2Sw'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Master Text Animation in CapCut (Under 10 Minutes)', 'Text animation · Patrik Key', 'https://www.youtube.com/watch?v=-KrVdWPhMV8', NULL, 72, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=-KrVdWPhMV8'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '8 Most Viral Text Effects Inside CapCut - Detailed Tutorial', 'Text animation · Anmol_type.', 'https://www.youtube.com/watch?v=rfS9nA2RmJA', NULL, 73, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=rfS9nA2RmJA'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Typography Motion Graphics Animation in CapCut', 'Text animation · Matt Loui', 'https://www.youtube.com/watch?v=Shgr6TkJTDI', NULL, 74, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Shgr6TkJTDI'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '8 Premium CapCut Text Effects', 'Text animation · Matt Loui', 'https://www.youtube.com/watch?v=rFD_U3OZzTk', NULL, 75, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=rFD_U3OZzTk'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '11 Best Free Text Effects in CapCut', 'Text animation · Matt Loui', 'https://www.youtube.com/watch?v=3TtgmGpnNKQ', NULL, 76, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=3TtgmGpnNKQ'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '10 Viral CapCut Text Effects', 'Text animation · Matt Loui', 'https://www.youtube.com/watch?v=WNS-n_O21TQ', NULL, 77, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=WNS-n_O21TQ'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Make a Typography Edit in CapCut', 'Text animation · BeTheCut', 'https://www.youtube.com/watch?v=T0GAkQPAmVI', NULL, 78, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=T0GAkQPAmVI'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Make Smooth Animated Captions in CapCut (Step-by-Step)', 'Text animation · dayotheecreator', 'https://www.youtube.com/watch?v=D0Z0PdBcfpQ', NULL, 79, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=D0Z0PdBcfpQ'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Viral Text Animation in CapCut - Beginner to Advanced MasterClass', 'Text animation · Cr8ive Kane', 'https://www.youtube.com/watch?v=ss9LAZqUroY', NULL, 80, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=ss9LAZqUroY'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Make Viral Captions in CapCut', 'Text animation · Aeovisuals', 'https://www.youtube.com/watch?v=oUU1Lj7hcNU', NULL, 81, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=oUU1Lj7hcNU'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Create 10 Viral Caption Styles Using CapCut', 'Text animation · Patrik Key', 'https://www.youtube.com/watch?v=Q04FF5KZyt4', NULL, 82, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=Q04FF5KZyt4'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Keyframe Mastery Guide in CapCut - For Next Level Edits', 'Keyframes · Zeven 7', 'https://www.youtube.com/watch?v=fKNDlT5JbTY', NULL, 83, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=fKNDlT5JbTY'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Keyframe Mastery: Level Up Your Edits', 'Keyframes · Collin Michael', 'https://www.youtube.com/watch?v=4eLZq5VgkBk', NULL, 84, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=4eLZq5VgkBk'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Use Keyframes in CapCut Video Editor', 'Keyframes · Collin Michael', 'https://www.youtube.com/watch?v=AmfGZA_-tIQ', NULL, 85, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=AmfGZA_-tIQ'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Keyframe Mastery Explained | CapCut Tutorial', 'Keyframes · Rene Mlekuz', 'https://www.youtube.com/watch?v=nbER-edESt8', NULL, 86, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=nbER-edESt8'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'The Only CapCut Keyframe Tutorial You''ll Ever Need (Smooth Animation)', 'Keyframes · Graphic Guru', 'https://www.youtube.com/watch?v=NhXA_Y34w0g', NULL, 87, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=NhXA_Y34w0g'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Use Keyframes in CapCut (The Way Pros Actually Do It)', 'Keyframes · Dina and Agnes', 'https://www.youtube.com/watch?v=4owzZ1q15Nc', NULL, 88, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=4owzZ1q15Nc'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Keyframe Graphs Tutorial - Smooth Animation in CapCut', 'Keyframes · AVR.EDIT1', 'https://www.youtube.com/watch?v=LixgCbC0QqI', NULL, 89, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=LixgCbC0QqI'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Master Keyframing in CapCut PC: A Complete Beginner Guide', 'Keyframes · Joey Edits', 'https://www.youtube.com/watch?v=DI-ziBV87Ho', NULL, 90, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=DI-ziBV87Ho'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '5 Must-Know Masking Tips in CapCut PC', 'Masking · Rene Mlekuz', 'https://www.youtube.com/watch?v=rrIMVxRPidI', NULL, 91, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=rrIMVxRPidI'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Use Masking in CapCut for Cool Effects', 'Masking · Collin Michael', 'https://www.youtube.com/watch?v=42VONJM74fs', NULL, 92, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=42VONJM74fs'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT '5 Creative Masking Effects in CapCut', 'Masking · EditStudio', 'https://www.youtube.com/watch?v=v9Qxc7curqk', NULL, 93, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=v9Qxc7curqk'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Why You Should Be Using CapCut (Masking Tutorial)', 'Masking · Fusion Frames', 'https://www.youtube.com/watch?v=mF6rK8AW0_4', NULL, 94, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=mF6rK8AW0_4'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Tutorial: 5 Creative Editing Tricks Using Masks', 'Masking · Deny King', 'https://www.youtube.com/watch?v=NZPz0v-51i8', NULL, 95, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=NZPz0v-51i8'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Mask in CapCut PC - Beginners Tutorial', 'Masking · Joey Edits', 'https://www.youtube.com/watch?v=w-8P5qI2kfo', NULL, 96, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=w-8P5qI2kfo'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Fast, Easy & Free! Cinematic Color Grading in CapCut', 'Color grading · Joris Hermans', 'https://www.youtube.com/watch?v=TMnBLZy3WFg', NULL, 97, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=TMnBLZy3WFg'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'Master CapCut Color Grading in 30 Minutes', 'Color grading · Content Creators', 'https://www.youtube.com/watch?v=6ZQLtduEbZ4', NULL, 98, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=6ZQLtduEbZ4'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'CapCut Color Grading 101 (Tutorial)', 'Color grading · Collin Michael', 'https://www.youtube.com/watch?v=3-AGFutV8CU', NULL, 99, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=3-AGFutV8CU'
      AND library = 'design'
);

INSERT INTO videos (title, description, youtube_url, thumbnail_url, sort_order, library, created_at, updated_at)
SELECT 'How to Color Grade Like a Pro in CapCut - Free Course (Part 1)', 'Color grading · Joey Edits', 'https://www.youtube.com/watch?v=kCvbnf3IFRc', NULL, 100, 'design', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM videos
    WHERE youtube_url = 'https://www.youtube.com/watch?v=kCvbnf3IFRc'
      AND library = 'design'
);


-- ---------------------------------------------------------------------
-- 5. OPTIONAL — new permission names (data only)
-- ---------------------------------------------------------------------
-- database/seeders/RolePermissionSeeder.php gained 7 permission names in
-- this session:
--     documents.download
--     attendance.viewAny
--     attendance.manageSection
--     videos.viewAny
--     videos.create
--     videos.update
--     videos.delete
--
-- IMPORTANT: this seeder was modified but NEVER executed against the
-- dev database. `permissions` and `role_has_permissions` are both empty
-- there, and every new feature works anyway, because authorization is
-- done with role checks and policies (VideoPolicy, MemberPolicy, ...),
-- not with these permission rows.
--
-- So this block is not required to deploy the schema. Keep it if you
-- want production to mirror the seeder; skip it otherwise. Role →
-- permission mapping (member + extras for section_chef, rh_manager,
-- media_manager, design_manager, assistant_general, and the extra
-- grants for admin/vice_president) is best applied with:
--     php artisan db:seed --class=RolePermissionSeeder --force

INSERT INTO permissions (name, guard_name, created_at, updated_at)
SELECT v.name, 'web', NOW(), NOW()
FROM (
    SELECT 'documents.download'     AS name
    UNION ALL SELECT 'attendance.viewAny'
    UNION ALL SELECT 'attendance.manageSection'
    UNION ALL SELECT 'videos.viewAny'
    UNION ALL SELECT 'videos.create'
    UNION ALL SELECT 'videos.update'
    UNION ALL SELECT 'videos.delete'
) AS v
WHERE NOT EXISTS (
    SELECT 1 FROM permissions p
    WHERE p.name = v.name
      AND p.guard_name = 'web'
);

-- Clear Spatie's permission cache after any role/permission write:
--     php artisan permission:cache-reset


-- ---------------------------------------------------------------------
-- 6. VERIFICATION (run after the statements above)
-- ---------------------------------------------------------------------
-- Expect all 8 of: admin, president, member, section_chef, rh_manager,
-- media_manager, design_manager, assistant_general.
-- SELECT name FROM roles WHERE guard_name = 'web' ORDER BY name;

-- Expect exactly one row per table, InnoDB, utf8mb4.
-- SELECT table_name, engine, table_collation
-- FROM information_schema.tables
-- WHERE table_schema = DATABASE()
--   AND table_name IN ('attendance_records', 'videos');

-- Expect 4 FK/index entries on attendance_records and 1 on videos.
-- SELECT table_name, constraint_name, column_name,
--        referenced_table_name, referenced_column_name
-- FROM information_schema.key_column_usage
-- WHERE table_schema = DATABASE()
--   AND table_name IN ('attendance_records', 'videos')
-- ORDER BY table_name, constraint_name;

-- Expect 100 / 100 / 200 when the optional seed data was applied;
-- 0 / 0 / 0 when it was skipped.
-- SELECT library, COUNT(*) AS total FROM videos GROUP BY library;


-- ---------------------------------------------------------------------
-- 7. MIGRATION BOOKKEEPING (only if you used this SQL file, not artisan)
-- ---------------------------------------------------------------------
-- Laravel records applied migrations in `migrations` (columns:
-- id, migration, batch). If production gets its schema from this file,
-- the four migrations must be marked as run so a later
-- `php artisan migrate` stays a no-op. Batch = current max + 1, which
-- matches what Laravel would have used.
--
-- INSERT INTO migrations (migration, batch)
-- SELECT v.migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations)
-- FROM (
--     SELECT '2026_11_15_100000_create_club_management_roles' AS migration
--     UNION ALL SELECT '2026_11_15_110000_create_attendance_records_table'
--     UNION ALL SELECT '2026_11_15_120000_create_videos_table'
--     UNION ALL SELECT '2026_11_15_130000_add_library_to_videos_table'
-- ) AS v
-- WHERE NOT EXISTS (
--     SELECT 1 FROM migrations m WHERE m.migration = v.migration
-- );


-- ---------------------------------------------------------------------
-- 8. ROLLBACK (destructive — only for a failed deployment)
-- ---------------------------------------------------------------------
-- Order matters: video/attendance rows go first, then the role pivots,
-- then the roles, then the tables.
--
-- DELETE FROM `videos`;
-- DROP TABLE IF EXISTS `attendance_records`;
-- DROP TABLE IF EXISTS `videos`;
--
-- DELETE FROM model_has_roles
--  WHERE role_id IN (SELECT id FROM roles
--                    WHERE guard_name = 'web'
--                      AND name IN ('section_chef', 'rh_manager',
--                                   'media_manager', 'design_manager',
--                                   'assistant_general'));
-- DELETE FROM roles
--  WHERE guard_name = 'web'
--    AND name IN ('section_chef', 'rh_manager', 'media_manager',
--                 'design_manager', 'assistant_general');
--
-- DELETE FROM migrations WHERE migration LIKE '2026_11_15_%';
--
-- php artisan permission:cache-reset

-- =====================================================================
-- END OF FILE
-- =====================================================================
