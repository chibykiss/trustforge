<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Forms\Components\Group;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransaction extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query( Transaction::query()

            )
            ->defaultSort('updated_at','desc')
            ->columns([
                TextColumn::make('user.name')
                ->label('User'),

                ImageColumn::make('fromAsset.logo')
                ->label('From'),

                // TextColumn::make('coin_amount')
                // ->description(fn (Transaction $record): string => $record->dollar_amount),

                TextColumn::make('coin_amount')
                ->label('From Amount')
                ->formatStateUsing(function(string $state, Transaction $record){
                    return $record->coin_amount.' $'.$record->dollar_amount;
                })
                ->listWithLineBreaks()
                ->wrap()
                ->lineClamp(2),


                ImageColumn::make('toAsset.logo')
                ->label('To'),

                TextColumn::make('to_amount')
                ->label('To Amount')
                ->formatStateUsing(function(string $state, Transaction $record){
                    return $record->to_amount.' $'.$record->to_dollar;
                })
                ->listWithLineBreaks()
                ->wrap()
                ->lineClamp(2),

                TextColumn::make('fee_coin')
                ->label('Fee')
                ->formatStateUsing(function(string $state, Transaction $record){
                    return $record->fee_coin.' $'.$record->fee_dollar;
                })
                ->listWithLineBreaks()
                ->wrap()
                ->lineClamp(2),

                TextColumn::make('type')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'swap' => 'gray',
                    'recieve' => 'success',
                    'send' => 'danger',
                })
                ->sortable(),


                TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'completed' => 'success',
                })
                ->sortable(),

                TextColumn::make('created_at')
                ->dateTime()

            ]);
    }
}
