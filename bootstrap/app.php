<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [\App\Http\Middleware\EnforceIdleSessionLock::class]);

        $middleware->alias([
            'check.user.type' => \App\Http\Middleware\CheckUserType::class,
            'ensure.patient.profile' => \App\Http\Middleware\EnsurePatientProfileExists::class,
            'privacy.accepted' => \App\Http\Middleware\EnsurePrivacyAgreementAccepted::class,
            'emergency.admin' => \App\Http\Middleware\EnsureEmergencyAdministrator::class,
            'sensitive.no-store' => \App\Http\Middleware\NoStoreSensitiveResponse::class,
            'clinical.operator' => \App\Http\Middleware\EnsureClinicalOperator::class,
            'teacher.safety' => \App\Http\Middleware\EnsureTeacherSafetyAccess::class,
            'system.admin' => \App\Http\Middleware\EnsureSystemAdministrator::class,
            'medical.audit' => \App\Http\Middleware\AuditMedicalAccess::class,
            'infrastructure.audit' => \App\Http\Middleware\RecordInfrastructureActivity::class,
            'patient.self' => \App\Http\Middleware\EnsurePatientSelfService::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\App\Exceptions\AuditUnavailableException $exception, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $exception->getMessage()], 503);
            }

            return response()->view('errors.audit-unavailable', ['message' => $exception->getMessage()], 503);
        });
    })->create();
