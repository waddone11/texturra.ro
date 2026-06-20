<?php

namespace Tests\Feature\Models;

use App\Models\Color;
use App\Models\ColorGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Faza 4 Grup A.1 regression.
 *
 * SKIPPED until the model fix: Color::$fillable lists 'css_code' but the real
 * column (confirmed against the prod dump in Faza 1) is 'cod_css', so the value
 * is silently dropped and the NOT NULL insert fails. Faza 4 corrects $fillable
 * to 'cod_css' and removes this skip.
 */
class ColorFillableTest extends TestCase
{
    use RefreshDatabase;

    public function test_color_cod_css_is_mass_assignable(): void
    {
        $group = ColorGroup::create(['name' => 'Rosii', 'image_path' => 'colors/rosii.png']);

        $color = Color::create([
            'color_group_id' => $group->id,
            'name'           => 'Rosu',
            'cod_css'        => '#ff0000',
        ]);

        $this->assertSame('#ff0000', $color->fresh()->cod_css);
    }
}
