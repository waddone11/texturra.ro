<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * URL-based deploy helper for cPanel hosting without terminal access.
 *
 * STRICT WHITELIST: only idempotent / non-destructive commands. There is
 * deliberately NO route to migrate / migrate:fresh / db:seed / db:wipe /
 * queue:* or anything that writes to the DB or can block production.
 *
 * Gated by App\Http\Middleware\VerifySecretKey (404 without a valid secret).
 */
class CommandController extends Controller
{
    /** route slug => safe artisan command. */
    public const COMMANDS = [
        'clear-cache'         => 'cache:clear',
        'config-clear'        => 'config:clear',
        'route-clear'         => 'route:clear',
        'view-clear'          => 'view:clear',
        'optimize-clear'      => 'optimize:clear',
        'optimize'            => 'optimize',
        'create-storage-link' => 'storage:link',
        'migrate-status'      => 'migrate:status', // read-only
        'about'               => 'about',
    ];

    /** List the available safe commands (text/plain). */
    public function index()
    {
        $secret = request()->query('secret') ? '?secret=' . request()->query('secret') : '';
        $lines = ["TEXTURRA — comenzi sigure de deploy (doar idempotente / ne-distructive)", str_repeat('-', 60)];
        foreach (self::COMMANDS as $slug => $cmd) {
            $lines[] = sprintf('  /commands/%-22s → php artisan %s', $slug . $secret, $cmd);
        }
        return $this->plain(implode("\n", $lines));
    }

    public function clearCache()        { return $this->execute('clear-cache'); }
    public function configClear()       { return $this->execute('config-clear'); }
    public function routeClear()        { return $this->execute('route-clear'); }
    public function viewClear()         { return $this->execute('view-clear'); }
    public function optimizeClear()     { return $this->execute('optimize-clear'); }
    public function optimize()          { return $this->execute('optimize'); }
    public function createStorageLink() { return $this->execute('create-storage-link'); }
    public function migrateStatus()     { return $this->execute('migrate-status'); }
    public function about()             { return $this->execute('about'); }

    /** Run a single whitelisted command and return its output. */
    protected function execute(string $slug)
    {
        // Defensive: only ever run a command that is in the whitelist.
        $command = self::COMMANDS[$slug] ?? abort(404);

        Log::channel('commands')->info('command executed', [
            'slug'    => $slug,
            'command' => $command,
            'ip'      => request()->ip(),
            'at'      => now()->toIso8601String(),
        ]);

        Artisan::call($command);

        return $this->plain("\$ php artisan {$command}\n\n" . Artisan::output());
    }

    protected function plain(string $body)
    {
        return response($body, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
