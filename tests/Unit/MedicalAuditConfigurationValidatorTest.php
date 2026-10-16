<?php

namespace Tests\Unit;

use App\Exceptions\AuditUnavailableException;
use App\Services\MedicalAuditConfigurationValidator;
use Tests\TestCase;

class MedicalAuditConfigurationValidatorTest extends TestCase
{
    public function test_local_environment_does_not_require_external_boundary_by_default(): void
    {
        $this->assertFalse(config('medical_security.audit.require_external_boundary'));
    }

    public function test_safe_https_collector_configuration_passes(): void
    {
        config()->set('medical_security.audit', [
            'require_external_boundary' => true,
            'fail_closed' => true,
            'separation_confirmed' => true,
            'signing_key' => str_repeat('a', 64),
            'driver' => 'http',
            'http_endpoint' => 'https://audit.example.test/events',
            'http_token' => 'collector-token',
        ]);

        (new MedicalAuditConfigurationValidator())->assertSecure();

        $this->addToAssertionCount(1);
    }

    public function test_unconfirmed_control_boundary_is_rejected(): void
    {
        config()->set('medical_security.audit', [
            'require_external_boundary' => true,
            'fail_closed' => true,
            'separation_confirmed' => false,
            'signing_key' => str_repeat('a', 64),
            'driver' => 'http',
            'http_endpoint' => 'https://audit.example.test/events',
            'http_token' => 'collector-token',
        ]);

        $this->expectException(AuditUnavailableException::class);
        (new MedicalAuditConfigurationValidator())->assertSecure();
    }

    public function test_primary_database_cannot_be_reused_as_the_audit_database(): void
    {
        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql', [
            'host' => 'db.internal', 'port' => '3306', 'database' => 'clinic', 'username' => 'clinic_app',
        ]);
        config()->set('database.connections.audit', [
            'host' => 'db.internal', 'port' => '3306', 'database' => 'clinic', 'username' => 'clinic_app',
        ]);
        config()->set('medical_security.audit', [
            'require_external_boundary' => true,
            'fail_closed' => true,
            'separation_confirmed' => true,
            'signing_key' => str_repeat('a', 64),
            'driver' => 'database',
            'connection' => 'audit',
        ]);

        $this->expectException(AuditUnavailableException::class);
        (new MedicalAuditConfigurationValidator())->assertSecure();
    }
}
