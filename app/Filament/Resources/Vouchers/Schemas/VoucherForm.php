<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Voucher')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Cod')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Toggle::make('active')
                            ->label('Activ')
                            ->default(true),

                        TextInput::make('discount_amount')
                            ->label('Sumă reducere')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('RON')
                            ->helperText('Sumă fixă SAU procent — completează unul.'),

                        TextInput::make('discount_percentage')
                            ->label('Procent reducere')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),

                        DatePicker::make('valid_from')
                            ->label('Valabil de la')
                            ->native(false),

                        DatePicker::make('valid_to')
                            ->label('Valabil până la')
                            ->native(false)
                            ->afterOrEqual('valid_from'),

                        TextInput::make('usage_limit')
                            ->label('Limită utilizări')
                            ->numeric()
                            ->minValue(0)
                            ->default(1)
                            ->required(),

                        // counter incremented at checkout — read-only here (not hand-edited)
                        TextInput::make('times_used')
                            ->label('Utilizat de')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Contor automat (la aplicarea în checkout). Read-only.'),
                    ]),
            ]);
    }
}
