<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyAgreementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_current_consent_is_sent_to_agreement_after_login(): void
    {
        $user = User::factory()->create([
            'privacy_terms_version' => null,
            'privacy_terms_accepted_at' => null,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('privacy-agreement.show', absolute: false));
    }

    public function test_user_with_current_consent_continues_to_dashboard_after_login(): void
    {
        $user = User::factory()->create([
            'privacy_terms_version' => (string) config('privacy.terms_version'),
            'privacy_terms_accepted_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_user_can_accept_the_current_agreement(): void
    {
        $user = User::factory()->create([
            'privacy_terms_version' => null,
            'privacy_terms_accepted_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/privacy-agreement/accept', [
            'agreement' => '1',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));

        $user->refresh();
        $this->assertSame((string) config('privacy.terms_version'), $user->privacy_terms_version);
        $this->assertNotNull($user->privacy_terms_accepted_at);
    }

    public function test_declining_logs_user_out_and_returns_to_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/privacy-agreement/decline');

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_old_agreement_version_cannot_access_protected_routes(): void
    {
        $user = User::factory()->create([
            'privacy_terms_version' => 'old-version',
            'privacy_terms_accepted_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('privacy-agreement.show', absolute: false));
    }
}
