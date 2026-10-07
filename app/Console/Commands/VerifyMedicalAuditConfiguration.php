<?php

namespace App\Console\Commands;

use App\Services\MedicalAuditConfigurationValidator;
use App\Services\MedicalAuditService;
use Illuminate\Console\Command;
use Throwable;

class VerifyMedicalAuditConfiguration extends Command
{
    protected $signature = 'medical:audit-check {--write-probe : Append a permanent configuration-test event to the external audit sink}';

    protected $description = 'Validate the external append-only medical audit boundary and optionally test a permanent write';

    public function handle(
        MedicalAuditConfigurationValidator $configuration,
        MedicalAuditService $audit,
    ): int {
        try {
            $configuration->assertSecure();
            $this->info('Medical audit configuration passed separation and fail-closed checks.');

            if ($this->option('write-probe')) {
                $audit->record(null, 'WRITE', 'medical_audit_connectivity_probe', null, [
                    'resource_type' => 'AuditInfrastructure',
                    'resource_id' => 'deployment-check',
                ]);
                $this->info('A permanent connectivity probe was appended to the external audit sink.');
            } else {
                $this->line('Use --write-probe during deployment to verify actual append connectivity.');
            }

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }
}
