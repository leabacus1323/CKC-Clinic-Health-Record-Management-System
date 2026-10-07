<?php

namespace App\Services;

use App\Exceptions\AuditUnavailableException;
use Illuminate\Support\Str;

class MedicalAuditConfigurationValidator
{
    private bool $validated = false;

    public function assertSecure(): void
    {
        if ($this->validated || ! (bool) config('medical_security.audit.require_external_boundary', false)) {
            $this->validated = true;
            return;
        }

        if (! (bool) config('medical_security.audit.fail_closed', true)) {
            $this->fail('MEDICAL_AUDIT_FAIL_CLOSED must be true when external audit enforcement is enabled.');
        }

        if (! (bool) config('medical_security.audit.separation_confirmed', false)) {
            $this->fail('The external audit control boundary has not been confirmed. Set MEDICAL_AUDIT_SEPARATION_CONFIRMED=true only after deploying the collector/database under separate administration.');
        }

        $signingKey = (string) config('medical_security.audit.signing_key');
        if (strlen($signingKey) < 32) {
            $this->fail('MEDICAL_AUDIT_SIGNING_KEY must be a dedicated random secret of at least 32 characters.');
        }

        $driver = (string) config('medical_security.audit.driver');
        match ($driver) {
            'http' => $this->validateHttp(),
            'database' => $this->validateDatabase(),
            default => $this->fail('Production medical auditing must use the http or database driver.'),
        };

        $this->validated = true;
    }

    private function validateHttp(): void
    {
        $endpoint = (string) config('medical_security.audit.http_endpoint');
        if (! Str::startsWith($endpoint, 'https://')) {
            $this->fail('MEDICAL_AUDIT_HTTP_ENDPOINT must use HTTPS.');
        }

        if ((string) config('medical_security.audit.http_token') === '') {
            $this->fail('MEDICAL_AUDIT_HTTP_TOKEN is required for the external audit collector.');
        }
    }

    private function validateDatabase(): void
    {
        $auditConnection = (string) config('medical_security.audit.connection', 'audit');
        $primaryConnection = (string) config('database.default');

        if ($auditConnection === $primaryConnection) {
            $this->fail('The medical audit connection must be different from the primary application connection.');
        }

        $audit = (array) config("database.connections.{$auditConnection}", []);
        $primary = (array) config("database.connections.{$primaryConnection}", []);

        if ($audit === []) {
            $this->fail("Audit database connection [{$auditConnection}] is not configured.");
        }

        $sameServerAndDatabase = (string) ($audit['host'] ?? '') === (string) ($primary['host'] ?? '')
            && (string) ($audit['port'] ?? '') === (string) ($primary['port'] ?? '')
            && (string) ($audit['database'] ?? '') === (string) ($primary['database'] ?? '');

        if ($sameServerAndDatabase) {
            $this->fail('The medical audit database cannot be the primary application database.');
        }

        if ((string) ($audit['username'] ?? '') === (string) ($primary['username'] ?? '')) {
            $this->fail('The medical audit writer must use a dedicated INSERT-only credential, not the primary database account.');
        }
    }

    private function fail(string $message): never
    {
        throw new AuditUnavailableException('Unsafe medical audit configuration: '.$message);
    }
}
