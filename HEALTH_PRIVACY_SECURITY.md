# Health Privacy, Consent, Emergency Access, and Immunization Module

## Purpose

This module separates student and non-student health profiles, manages guardian relationships and granular authorizations, provides controlled emergency access, and tracks immunization compliance.

It is a technical control set, not a legal certification. Configure the terminology and workflows with the institution's Data Protection Officer and legal adviser before production deployment.

## Roles

| Role | User type | Main access |
|---|---:|---|
| Clinic Administrator | 0 | User administration, guardian verification, vaccine-rule configuration, existing clinic administration |
| Student / Patient | 1 | Own profile, own authorizations when consent authority has transferred, immunization uploads |
| Clinical Staff | 2 | Existing clinical workflows and immunization review |
| Doctor | 3 | Existing clinical workflows and immunization review |
| Faculty / Patient | 4 | Own non-student health profile and authorizations |
| Guardian | 5 | Approved minor-student records and consent management when permitted |
| Principal / Emergency Administrator | 6 | Emergency console and security audit only |

The Principal role cannot enter ordinary clinical resource routes. Emergency access is the only path to vital health information for this role.

## Dual Registries

Each patient profile contains:

- `registry_type`: `student` or `non_student`
- `privacy_regime`: configurable label such as `ferpa_student`, `hipaa_workforce`, or `philippines_dpa`

The values are set automatically from `patient_type` and can be configured through environment variables before migration.

## Student Consent Authority

Set one of the following:

```env
STUDENT_RIGHTS_TRANSFER_MODE=age_18
```

- `age_18`: approved guardian controls consent while the student is under 18.
- `postsecondary`: the attending student controls consent regardless of age.

Use `postsecondary` for institutions whose policy or applicable law transfers record rights when a learner attends a postsecondary institution.

## Guardian Workflow

1. The student profile owner invites a guardian using the guardian's email address.
2. The guardian signs in using a Guardian account and accepts the invitation.
3. A Clinic Administrator verifies the claimed legal relationship.
4. Access begins only after both acceptance and administrator approval.
5. The relationship can be disabled. After consent rights transfer, the guardian needs a separate granular authorization.

Guardian approval notes, approver, and timestamps are retained and audited.

## Granular Electronic Authorization

An authorized adult student, staff/faculty patient, or approved guardian can create an authorization containing:

- Exact recipient name and email
- Purpose of disclosure
- Selected data categories
- Required expiration date
- Exact authorization text shown at signing
- Password re-entry
- Electronic signature name
- Form version, IP address, browser information, and signature timestamp
- HMAC-SHA256 verification fingerprint

Authorizations can be revoked in the portal. Recipients see only the data categories granted by currently active authorizations. The signed form can be viewed and printed by the subject, grantor, approved guardian, or named recipient.

To publish revised authorization language, increment:

```env
HEALTH_AUTHORIZATION_VERSION=1.0
```

## Emergency Break-Glass Protocol

Only a user with type `6` can activate emergency access.

Activation requires:

- Current-password re-authentication
- Description of the actual threat
- Written justification
- Named recipient or emergency viewer
- Explicit confirmation
- Rate limiting

The grant lasts for a configurable period, limited to 30 minutes by the service:

```env
BREAK_GLASS_MINUTES=10
PRIVACY_OFFICER_EMAIL=privacy@example.edu
```

The emergency view exposes only identity, emergency contact, allergies, blood type, and known conditions. Prescriptions, attachments, examinations, detailed notes, and routine visits remain hidden.

Every console view, activation-form view, activation, emergency-record view, and audit-log review is recorded.

## Audit Integrity

`access_audit_logs` and `break_glass_events` are append-only through Eloquent model guards. On MySQL/MariaDB, database triggers reject `UPDATE` and `DELETE` statements.

Every record contains its own SHA-256 hash and the previous record's hash. The audit page recalculates both chains and reports the first mismatch.

These controls make the logs application-immutable and tamper-evident. A database owner with enough privilege could still drop triggers or alter infrastructure; use restricted database accounts, encrypted backups, centralized log shipping, and independent retention controls for stronger assurance.

## Immunization Compliance

Clinic Administrators configure requirements by registry:

- Requirement code and vaccine name
- Student, non-student, or all profiles
- Minimum verified doses
- Due age in months
- Booster interval
- Institution-wide deadline
- Active/inactive status

The portal calculates these states:

- compliant
- pending review
- not due
- due soon
- booster due
- overdue
- missing
- expired or incomplete

Students, adult profile owners, and approved guardians can upload PDF/JPG/PNG records up to 10 MB. Files are stored on Laravel's private local disk outside the public web directory. Each file is renamed with a UUID and recorded with a SHA-256 checksum. Downloads and reviews are audited.

## Sensitive Response Controls

Privacy, emergency, guardian-verification, and immunization-review responses include headers that prevent browser/proxy caching and MIME sniffing.

## Environment Configuration

```env
BREAK_GLASS_MINUTES=10
PRIVACY_OFFICER_EMAIL=
CONSENT_MAX_MONTHS=24
HEALTH_AUTHORIZATION_VERSION=1.0
STUDENT_RIGHTS_TRANSFER_MODE=age_18
STUDENT_PRIVACY_REGIME=ferpa_student
NON_STUDENT_PRIVACY_REGIME=hipaa_workforce
```

For a Philippine deployment, choose labels and policy text approved for compliance with the Data Privacy Act of 2012 and the institution's actual legal status. Do not label a record HIPAA-covered merely because it belongs to an employee.

## Deployment

Back up the database and uploaded files, then run:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
```

Create at least one Principal / Emergency Administrator account through Admin > Users. Configure the privacy-officer email and test outbound mail before relying on alerts.

## Operational Checks Before Launch

1. Verify guardian identity and legal relationship using an approved institutional procedure.
2. Configure vaccine requirements from the institution's current official health policy.
3. Test one authorization for each scope and confirm that unselected categories remain hidden.
4. Test emergency activation, expiration, audit notification, and denial for ordinary administrators.
5. Confirm private files cannot be reached by a direct public URL.
6. Restrict the production database account from dropping triggers or altering audit tables.
7. Establish audit-log retention, backup, incident response, and breach-notification procedures.
