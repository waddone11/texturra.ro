<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Order lifecycle status. The values mirror the existing MySQL enum on
 * orders.status — every value present in real data ("placed") plus the logical
 * lifecycle states — so casting never invalidates an existing order.
 */
enum OrderStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Placed = 'placed';
    case Processing = 'processing';
    case Completed = 'completed';
    case Canceled = 'canceled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'În așteptare',
            self::Placed => 'Plasată',
            self::Processing => 'În procesare',
            self::Completed => 'Finalizată',
            self::Canceled => 'Anulată',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Placed => 'info',
            self::Processing => 'info',
            self::Completed => 'success',
            self::Canceled => 'danger',
        };
    }
}
