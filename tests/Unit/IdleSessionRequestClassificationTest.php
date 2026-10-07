<?php

namespace Tests\Unit;

use App\Http\Middleware\EnforceIdleSessionLock;
use App\Models\User;
use App\Services\MedicalAuditService;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class IdleSessionRequestClassificationTest extends TestCase
{
    public function test_background_ajax_poll_does_not_extend_the_session(): void
    {
        $original = now()->subSeconds(60)->timestamp;
        $request = $this->request('GET', '/background-poll', [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $request->session()->put('security_last_activity_at', $original);

        $middleware = new EnforceIdleSessionLock($this->createMock(MedicalAuditService::class));
        $middleware->handle($request, fn (): Response => response('ok'));

        $this->assertSame($original, $request->session()->get('security_last_activity_at'));
    }

    public function test_document_navigation_refreshes_the_session(): void
    {
        $original = now()->subSeconds(60)->timestamp;
        $request = $this->request('GET', '/clinical-page', [
            'HTTP_ACCEPT' => 'text/html',
            'HTTP_SEC_FETCH_DEST' => 'document',
        ]);
        $request->session()->put('security_last_activity_at', $original);

        $middleware = new EnforceIdleSessionLock($this->createMock(MedicalAuditService::class));
        $middleware->handle($request, fn (): Response => response('ok'));

        $this->assertGreaterThan($original, $request->session()->get('security_last_activity_at'));
    }

    private function request(string $method, string $uri, array $server): Request
    {
        $request = Request::create($uri, $method, server: $server);
        $request->setLaravelSession(new Store('test-session', new ArraySessionHandler(120)));
        $request->setUserResolver(fn () => new User([
            'id' => 100,
            'name' => 'Test Nurse',
            'user_type' => User::TYPE_STAFF,
        ]));

        $route = (new Route([$method], ltrim($uri, '/'), fn () => null))->name('test.route');
        $request->setRouteResolver(fn () => $route);

        return $request;
    }
}
