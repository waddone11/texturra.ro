<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Faza 4 Grup B — remove the plaintext users.raw_password column (PII, audit S5).
 *
 * All code that wrote this column has been removed (ChangePassword,
 * RegisterController, ResetPasswordController) so dropping it is safe.
 *
 * NOTE: not yet applied to the prod dump. Existing rows may still contain
 * plaintext passwords; run this migration manually after confirming, and
 * consider a one-off `UPDATE users SET raw_password = NULL` is NOT needed
 * once the column is dropped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'raw_password')) {
                $table->dropColumn('raw_password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'raw_password')) {
                $table->string('raw_password')->nullable()->after('password');
            }
        });
    }
};
