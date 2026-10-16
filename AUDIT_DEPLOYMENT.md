# Permanent Medical Audit Deployment

The authoritative medical audit must be outside the Laravel administrator's control boundary. The application has no update, delete, truncate, or clear operation for this store.

## Local development

For `local`, `testing`, and `development`, the default audit connection is the application's database and the external-boundary check is disabled. Run migrations to create `medical_access_events`. This local configuration is not suitable for production: production and staging must use the separate audit connection or external collector below, and access remains fail-closed if that sink is unavailable.

## Preferred: external HTTPS collector

```env
MEDICAL_AUDIT_DRIVER=http
MEDICAL_AUDIT_FAIL_CLOSED=true
MEDICAL_AUDIT_SIGNING_KEY=GENERATE_A_DEDICATED_64_CHARACTER_RANDOM_SECRET
MEDICAL_AUDIT_REQUIRE_EXTERNAL_BOUNDARY=true
MEDICAL_AUDIT_SEPARATION_CONFIRMED=true
MEDICAL_AUDIT_HTTP_ENDPOINT=https://audit-collector.example.edu/v1/events
MEDICAL_AUDIT_HTTP_TOKEN=APPEND_ONLY_COLLECTOR_TOKEN
MEDICAL_AUDIT_HTTP_TIMEOUT_SECONDS=5
```

Collector requirements:

- authenticated HTTPS ingestion;
- accept only append operations;
- idempotency by `event_id` because network retries may repeat a request;
- retention-locked/WORM storage;
- no retention-administration credential on the Laravel host;
- independent encryption keys and backups;
- privacy/security officer ownership of reader and retention credentials.

## Alternative: separate MySQL/MariaDB server

Run `database/audit-schema/mysql.sql` on a different server using the audit-infrastructure owner. Replace all passwords and host placeholders first. The Laravel application receives only the INSERT-only `ckc_audit_writer` credential.

```env
MEDICAL_AUDIT_DRIVER=database
MEDICAL_AUDIT_DB_CONNECTION=audit
MEDICAL_AUDIT_TABLE=medical_access_events
AUDIT_DB_HOST=separate-audit-db.internal
AUDIT_DB_PORT=3306
AUDIT_DB_DATABASE=ckc_clinic_audit
AUDIT_DB_USERNAME=ckc_audit_writer
AUDIT_DB_PASSWORD=LONG_RANDOM_INSERT_ONLY_PASSWORD
AUDIT_MYSQL_ATTR_SSL_CA=/secure/path/audit-ca.pem
```

A normal database owner can still destroy a database. Replicate or stream the table to retention-locked storage if the requirement is that even infrastructure administrators cannot clear history.

## Deployment verification

After configuration and cache rebuild:

```bash
php artisan optimize:clear
php artisan optimize
php artisan medical:audit-check --write-probe
```

The probe is intentionally permanent. Sensitive routes remain unavailable whenever the audit service cannot accept an event.

## Audit payload

Events contain user/staff ID, role, UTC timestamp, `READ`/`WRITE`/`DELETE`/`EXPORT`, route, resource identifier, patient ID when resolvable, status, IP, browser, and HMAC. Clinical narrative, diagnoses, treatments, prescriptions, passwords, tokens, and file contents are excluded.
