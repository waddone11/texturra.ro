<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Public-surface smoke tests. These must stay green through the dead-code
 * removal in Faza 4 — they prove the live pages still boot.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk();
    }
}
