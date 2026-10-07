# Role-Based Medical Access, Inactivity Lock, and Permanent Audit

## Access model

| Role | Medical access |
|---|---|
| School Nurse / Clinic Staff (`user_type=2`) | Full read/write access to patient medical history, clinic records, examinations, medicines, prescriptions, immunizations, reports, and life-safety publishing. |
| School Doctor (`user_type=3`) | Full read/write access to the same clinical functions, including prescriptions and record exports. |
| Teacher (`user_type=7`) | Read-only access only to clinician-published student life-safety alerts, emergency medication, and response instructions. Clinical notes, diagnoses, visits, prescriptions, documents, and general treatment history are not selected or rendered. |
| System Administrator (`user_type=0`) | Account management, provider accounts, system configuration, vaccine-rule configuration, guardian verification, and sanitized infrastructure activity. No patient, appointment, examination, prescription, clinical-report, or medical-text route access. |
| Principal / Emergency Administrator (`user_type=6`) | No normal clinical access. Minimum-necessary emergency data is available only through the separately controlled break-glass workflow. |

Role restrictions are applied both to routes and controller data projections. Hiding menu links alone is not treated as authorization.

## Three-minute inactivity lock

The default lock threshold is 180 seconds, with a 30-second warning. The screen is redirected to a dedicated lock page and the current password is required to unlock it.

```env
MEDICAL_IDLE_TIMEOUT_SECONDS=180
MEDICAL_IDLE_WARNING_SECONDS=30
```

Real keyboard, mouse, touch, or scroll activity sends a throttled heartbeat. Background AJAX/dashboard polling does not refresh the server-side timer. Locked JSON requests return HTTP `423 Locked`; direct URL access and browser refresh do not bypass the password requirement.

## Permanent medical audit

Every protected medical route is covered by `medical.audit`. Successful reads, writes, deletes, downloads, prints, and exports append an event containing:

- immutable event ID;
- staff/user ID and role;
- UTC timestamp;
- action type: `READ`, `WRITE`, `DELETE`, or `EXPORT`;
- route, request method, response status, and resource identifier;
- patient ID when resolvable;
- source IP and browser agent;
- HMAC integrity signature.

Diagnosis text, symptoms, treatment notes, prescriptions, passwords, tokens, and uploaded-file contents are excluded from the audit payload.

Sensitive reads are not returned and sensitive writes are rolled back when the audit sink is unavailable. This is the fail-closed behavior.

## Mandatory external control boundary

For production, use either:

1. an HTTPS audit collector that writes to retention-locked/WORM storage; or
2. a physically separate database using the included schema and an **INSERT-only** application credential.

The Laravel system-administrator role has no screen or API that can update, delete, truncate, or clear the external audit store. The retention owner/reader credential must be held by a separate privacy, security, or infrastructure authority.

```env
MEDICAL_AUDIT_DRIVER=http
MEDICAL_AUDIT_FAIL_CLOSED=true
MEDICAL_AUDIT_SIGNING_KEY=GENERATE_A_DEDICATED_64_CHARACTER_RANDOM_SECRET
MEDICAL_AUDIT_REQUIRE_EXTERNAL_BOUNDARY=true
MEDICAL_AUDIT_SEPARATION_CONFIRMED=true
MEDICAL_AUDIT_HTTP_ENDPOINT=https://audit-collector.example.edu/v1/events
MEDICAL_AUDIT_HTTP_TOKEN=COLLECTOR_APPEND_TOKEN
```

For the separate-database option, configure the `AUDIT_DB_*` values and run `database/audit-schema/mysql.sql` on the separate server. Replace the host placeholders before executing it.

The application enforces these production checks:

- fail-closed mode is enabled;
- separation is explicitly confirmed;
- a dedicated signing key is configured;
- HTTP collectors use HTTPS and a token; or
- the audit connection, database, and writer credential differ from the primary application database.

Run this after deployment:

```bash
php artisan medical:audit-check --write-probe
```

The probe is a permanent audit event. A successful result confirms that the application can append to the external sink.

## Important limitation

No code running on the same machine can prevent an unrestricted operating-system or external-database owner from replacing application code or destroying infrastructure. The “cannot be altered or cleared by the system administrator” requirement is achieved only when the audit collector/database, retention policy, encryption keys, and owner credentials are administered outside the Laravel/system-administrator control boundary. WORM retention and independent backups are required for the strongest guarantee.
