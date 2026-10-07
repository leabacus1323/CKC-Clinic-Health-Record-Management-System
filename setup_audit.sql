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

GRANT INSERT ON ckc_clinic_audit.medical_access_events TO 'ckc_audit_writer'@'127.0.0.1';
FLUSH PRIVILEGES;
