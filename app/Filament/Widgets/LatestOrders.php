<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Tables;
use App\Models\Order;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\OrderResource;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderResource::getEloquentQuery()
            )
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('order id')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('name customer'),

                TextColumn::make('grand_total')
                    ->label('grand total')
                    ->money('IDR'),

                TextColumn::make('status')
                    ->label('status pesanan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger'
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle'
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('metode pembayaran')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payment_status')
                    ->label('status pembayaran ')
                    ->sortable()
                    ->badge()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('order date')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d F Y')),
            ])
            ->actions([

                Action::make('View Order')

                    ->url(fn(Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-m-eye')
            ]);
    }
}
