<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    protected static ?string $pollingInterval = '60s'; //set to null to disable it
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users',User::whereNotNull('phrase')->count())
                ->description('increase in users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->descriptionColor('success')
                ->chart([9,7,4,3,6,8,2,9])
                ->chartColor('success'),

            Stat::make('Total Transactions',Transaction::count())
                ->description('transactions')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->descriptionColor('success')
                ->chart([9,7,4,3,6,8,2,9])
                ->chartColor('success'),

            Stat::make('Crypto Assets',Asset::count())
                ->description('crypto assets')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->descriptionColor('success')
                ->chart([9,7,4,3,6,8,2,9])
                ->chartColor('success'),
            
        ];
    }
}
