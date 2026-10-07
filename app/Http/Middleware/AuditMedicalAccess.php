<?php

namespace App\Http\Middleware;

use App\Models\Patients;
use App\Services\MedicalAuditService;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuditMedicalAccess
{
    public function __construct(private readonly MedicalAuditService $audit)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $writeRequest = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);

        if ($writeRequest) {
            DB::beginTransaction();
        }

        try {
            $response = $next($request);

            if ($response->getStatusCode() < 400) {
                $this->audit->record(
                    $request->user(),
                    $this->actionType($request),
                    (string) ($request->route()?->getName() ?? 'unnamed_medical_route'),
                    $this->patientFromRequest($request),
                    [
                        'response_status' => $response->getStatusCode(),
                        'resource_type' => $this->resourceType($request),
                        'resource_id' => $this->resourceId($request),
                    ],
                    $request,
                );
            }

            if ($writeRequest) {
                DB::commit();
            }

            return $response;
        } catch (Throwable $exception) {
            if ($writeRequest && DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            throw $exception;
        }
    }

    private function actionType(Request $request): string
    {
        $name = strtolower((string) $request->route()?->getName());
        $path = strtolower($request->path());

        if (str_contains($name, 'export') || str_contains($name, 'download') || str_contains($name, 'print')
            || str_contains($path, 'export') || str_contains($path, 'download') || str_contains($path, 'print')) {
            return 'EXPORT';
        }

        return match ($request->method()) {
            'GET', 'HEAD' => 'READ',
            'DELETE' => 'DELETE',
            default => 'WRITE',
        };
    }

    private function patientFromRequest(Request $request): ?Patients
    {
        foreach ($request->route()?->parameters() ?? [] as $key => $value) {
            if ($value instanceof Patients) {
                return $value;
            }

            if ($value instanceof Model && isset($value->patient_id)) {
                return Patients::find($value->patient_id);
            }

            if (in_array($key, ['patient', 'patientId', 'patient_id'], true) && is_numeric($value)) {
                return Patients::find((int) $value);
            }
        }

        $patientId = $request->input('patient_id');

        return is_numeric($patientId) ? Patients::find((int) $patientId) : null;
    }

    private function resourceType(Request $request): ?string
    {
        foreach ($request->route()?->parameters() ?? [] as $value) {
            if ($value instanceof Model) {
                return class_basename($value);
            }
        }

        return $request->route()?->getName();
    }

    private function resourceId(Request $request): int|string|null
    {
        foreach ($request->route()?->parameters() ?? [] as $value) {
            if ($value instanceof Model) {
                return $value->getKey();
            }
        }

        foreach (['patientId', 'patient', 'id', 'record'] as $key) {
            $value = $request->route($key);
            if (is_scalar($value)) {
                return $value;
            }
        }

        return null;
    }
}
