<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The /commands deploy helper must be gated by a secret (404 without it) and must
 * only expose whitelisted, non-destructive commands.
 */
class CommandsRouteTest extends TestCase
{
    public function test_missing_secret_config_returns_404(): void
    {
        config(['commands.secret' => null]);

        $this->get('/commands/about?secret=anything')->assertNotFound();
        $this->get('/commands/about')->assertNotFound();
    }

    public function test_wrong_secret_returns_404(): void
    {
        config(['commands.secret' => 'the-real-secret']);

        $this->get('/commands/about?secret=wrong')->assertNotFound();
        $this->get('/commands/about')->assertNotFound();
    }

    public function test_correct_secret_runs_whitelisted_command(): void
    {
        config(['commands.secret' => 'the-real-secret']);

        $response = $this->get('/commands/about?secret=the-real-secret');

        $response->assertOk();
        $response->assertSee('about', false); // output echoes "$ php artisan about"
    }

    public function test_secret_via_header_works(): void
    {
        config(['commands.secret' => 'the-real-secret']);

        $this->withHeaders(['X-Command-Secret' => 'the-real-secret'])
            ->get('/commands/migrate-status')
            ->assertOk();
    }

    public function test_destructive_commands_have_no_route(): void
    {
        config(['commands.secret' => 'the-real-secret']);

        // None of these exist as routes — must 404 even with a valid secret.
        foreach (['migrate-fresh', 'migrate', 'db-seed', 'db-wipe', 'queue-work', 'queue-restart'] as $slug) {
            $this->get("/commands/{$slug}?secret=the-real-secret")
                ->assertNotFound();
        }
    }
}
