<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Secret key for the /commands deploy helper
    |--------------------------------------------------------------------------
    | Gate for the URL-based artisan command runner (cPanel without terminal).
    | EMPTY by default → ALL /commands routes return 404 (gate closed).
    | On production set a long random value in .env (COMMANDS_SECRET) — NEVER in git.
    */
    'secret' => env('COMMANDS_SECRET'),

    // Max requests per minute against /commands (anti brute-force).
    'rate_limit' => env('COMMANDS_RATE_LIMIT', 30),
];
