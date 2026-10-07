# Data Privacy Agreement Feature

## User flow

1. The user submits valid login credentials.
2. If the user has not accepted the current agreement version, the system displays the Data Privacy Agreement and Terms of Use.
3. **I Agree and Continue** records the agreement version, acceptance date/time, IP address, and browser user-agent, then continues to the user's role-based dashboard/profile flow.
4. **I Do Not Agree** logs the user out, invalidates the session, and returns to the login page.
5. Protected pages are guarded by middleware, so the agreement page cannot be bypassed by entering a dashboard URL directly.

## Deployment

Run the database migration:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
```

Optional `.env` settings:

```env
PRIVACY_TERMS_VERSION=1.0
PRIVACY_TERMS_EFFECTIVE_DATE="June 27, 2026"
PRIVACY_ORGANIZATION="Christ the King College Clinic"
PRIVACY_CONTACT_EMAIL=
```

Increase `PRIVACY_TERMS_VERSION` when the agreement changes materially. Users who accepted an older version will be asked to accept the new version.

## Review notice

The included agreement is a practical system template. The institution's Data Protection Officer or legal adviser should review the wording, contact details, retention periods, lawful bases, and institutional policies before production use.
