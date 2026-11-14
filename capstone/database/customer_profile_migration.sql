-- Customer profile migration for MITZTIANPC.
-- In phpMyAdmin, select the mitztianpc_wired_internet_services database
-- before importing this file.
--
-- customer_tbl.contact_number already stores the editable contact number.
-- user_accounts_tbl.password already stores the password hash; the profile
-- form updates it using PHP password_hash(), so no password column is added.
-- The only missing field required by the profile photo feature is
-- user_accounts_tbl.profile_photo.

SET @profile_schema = DATABASE();
SET @profile_photo_column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @profile_schema
      AND TABLE_NAME = 'user_accounts_tbl'
      AND COLUMN_NAME = 'profile_photo'
);

SET @profile_photo_migration = IF(
    @profile_photo_column_exists = 0,
    'ALTER TABLE `user_accounts_tbl` ADD COLUMN `profile_photo` VARCHAR(255) NULL DEFAULT NULL',
    'SELECT ''profile_photo column already exists'' AS migration_status'
);

PREPARE customer_profile_statement FROM @profile_photo_migration;
EXECUTE customer_profile_statement;
DEALLOCATE PREPARE customer_profile_statement;
