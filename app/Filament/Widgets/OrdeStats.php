<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdeStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Orderan Baru', Order::where('status', 'new')->count()),
            Stat::make('Orderan Diproses', Order::where('status', 'processing')->count()),
            Stat::make('Orderan DIkirim', Order::where('status', 'shipped')->count()),
            Stat::make('Orderan Terkirim', Order::where('status', 'delivered')->count()),
            Stat::make('Orderan Dibatalkan', Order::where('status', 'cancelled')->count()),

            Stat::make('Total Pendapatan', value: number_format(Order::sum('grand_total'), 0, ',', '.')),

        ];
    }
}
