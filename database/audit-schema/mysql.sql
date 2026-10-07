-- Run this on a SEPARATE audit server using a privileged audit-infrastructure account.
-- Do not give these owner credentials to the Laravel host administrator.
CREATE DATABASE IF NOT EXISTS ckc_clinic_audit
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ckc_clinic_audit;

CREATE TABLE IF NOT EXISTS medical_access_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id CHAR(26) NOT NULL,
    actor_user_id BIGINT UNSIGNED NULL,
    actor_role VARCHAR(100) NULL,
    patient_id BIGINT UNSIGNED NULL,
    action_type ENUM('READ','WRITE','DELETE','EXPORT') NOT NULL,
    event_name VARCHAR(191) NOT NULL,
    route_name VARCHAR(191) NULL,
    request_method VARCHAR(10) NULL,
    request_path VARCHAR(500) NULL,
    response_status SMALLINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(1000) NULL,
    resource_type VARCHAR(191) NULL,
    resource_id VARCHAR(191) NULL,
    metadata JSON NULL,
    integrity_hmac CHAR(64) NOT NULL,
    occurred_at TIMESTAMP NOT NULL,
    ingested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY medical_access_events_event_id_unique (event_id),
    KEY medical_access_events_actor_time_idx (actor_user_id, occurred_at),
    KEY medical_access_events_patient_time_idx (patient_id, occurred_at),
    KEY medical_access_events_action_time_idx (action_type, occurred_at)
) ENGINE=InnoDB;

-- A dedicated application credential can only append. It cannot read, update,
-- delete, truncate, alter, or drop audit data.
CREATE USER IF NOT EXISTS 'ckc_audit_writer'@'REPLACE_WITH_APP_SERVER_HOST'
    IDENTIFIED BY 'REPLACE_WITH_LONG_RANDOM_PASSWORD' REQUIRE SSL;
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'ckc_audit_writer'@'REPLACE_WITH_APP_SERVER_HOST';
GRANT INSERT ON ckc_clinic_audit.medical_access_events
    TO 'ckc_audit_writer'@'REPLACE_WITH_APP_SERVER_HOST';

-- Optional auditor account: SELECT only. Keep this credential outside the app.
CREATE USER IF NOT EXISTS 'ckc_audit_reader'@'REPLACE_WITH_AUDITOR_HOST'
    IDENTIFIED BY 'REPLACE_WITH_DIFFERENT_LONG_PASSWORD' REQUIRE SSL;
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'ckc_audit_reader'@'REPLACE_WITH_AUDITOR_HOST';
GRANT SELECT ON ckc_clinic_audit.medical_access_events
    TO 'ckc_audit_reader'@'REPLACE_WITH_AUDITOR_HOST';
FLUSH PRIVILEGES;

-- For the strongest guarantee, stream/replicate this table to storage with a
-- compliance retention lock (WORM). A database superuser can otherwise still
-- destroy data, which no Laravel code can prevent.
