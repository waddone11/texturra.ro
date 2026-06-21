<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class OrderForm
{
    /** Documented order lifecycle (status column is a free string; data is all "placed"). */
    public const STATUSES = [
        'pending' => 'În așteptare',
        'placed' => 'Plasată',
        'processing' => 'În procesare',
        'completed' => 'Finalizată',
        'canceled' => 'Anulată',
    ];

    public static function configure(Schema $schema): Schema
    {
        $addr = fn ($a): string => $a
            ? collect([$a->name, $a->street, $a->city, $a->state, $a->postal_code])->filter()->implode(', ')
            : '—';

        return $schema
            ->components([
                Section::make('Comandă')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('order_number')
                            ->label('Număr comandă')
                            ->content(fn (?Order $record): string => $record?->order_number ?? '—'),

                        Placeholder::make('created_at')
                            ->label('Data')
                            ->content(fn (?Order $record): string => $record?->created_at?->format('d.m.Y H:i') ?? '—'),

                        // The ONE editable thing (+ notes). No side-effects in code.
                        Select::make('status')
                            ->label('Status')
                            ->options(self::STATUSES)
                            ->required()
                            ->native(false),

                        Placeholder::make('payment_method')
                            ->label('Plată')
                            ->content(fn (?Order $record): string => $record?->payment_method ?? '—'),

                        Textarea::make('notes')
                            ->label('Note')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Client & adrese (read-only)')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('client')
                            ->label('Client')
                            ->content(fn (?Order $record): string => $record?->user
                                ? $record->user->name . ' · ' . $record->user->email
                                : '—'),

                        Placeholder::make('shipping')
                            ->label('Livrare')
                            ->content(fn (?Order $record): string => $addr($record?->shippingAddress)),

                        Placeholder::make('billing')
                            ->label('Facturare')
                            ->content(fn (?Order $record): string => $addr($record?->billingAddress)),
                    ]),

                // STORED totals — displayed read-only, never recalculated (price logic untouched).
                Section::make('Sume (stocate — read-only)')
                    ->columns(3)
                    ->schema([
                        Placeholder::make('total_amount')->label('Total')
                            ->content(fn (?Order $record): string => number_format((float) $record?->total_amount, 2) . ' RON'),
                        Placeholder::make('subtotal_excluding_vat')->label('Subtotal fără TVA')
                            ->content(fn (?Order $record): string => number_format((float) $record?->subtotal_excluding_vat, 2) . ' RON'),
                        Placeholder::make('total_vat')->label('TVA')
                            ->content(fn (?Order $record): string => number_format((float) $record?->total_vat, 2) . ' RON'),
                        Placeholder::make('discount')->label('Discount')
                            ->content(fn (?Order $record): string => number_format((float) $record?->discount, 2) . ' RON'),
                        Placeholder::make('shipping_cost')->label('Transport')
                            ->content(fn (?Order $record): string => number_format((float) $record?->shipping_cost, 2) . ' RON'),
                    ]),

                Section::make('Facturi / AWB / Voucher (read-only)')
                    ->collapsed()
                    ->schema([
                        Placeholder::make('invoices')
                            ->label('Facturi')
                            ->content(function (?Order $record): HtmlString {
                                $inv = $record?->invoices ?? collect();
                                if ($inv->isEmpty()) {
                                    return new HtmlString('— fără facturi —');
                                }

                                return new HtmlString($inv->map(fn ($i) => e(($i->invoice_number ?? ('#' . $i->id))
                                    . ' · ' . number_format((float) $i->total_amount, 2) . ' RON · ' . ($i->status ?? '—')))
                                    ->implode('<br>'));
                            }),

                        Placeholder::make('awb')
                            ->label('AWB')
                            ->content(fn (?Order $record): string => ($record?->awbLogs?->count() ?? 0) > 0
                                ? $record->awbLogs->map(fn ($a) => ($a->courier_type ?? '?') . ' · ' . ($a->status ?? '—'))->implode(' | ')
                                : ('— fără AWB —' . ($record?->awb_number ? ' (nr: ' . $record->awb_number . ')' : ''))),

                        Placeholder::make('voucher')
                            ->label('Voucher')
                            ->content(fn (?Order $record): string => $record?->voucherUsage
                                ? 'discount ' . number_format((float) $record->voucherUsage->discount, 2) . ' RON · ' . ($record->voucherUsage->status ?? '—')
                                : '— fără voucher —'),
                    ]),
            ]);
    }
}
