<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;

use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\OrderResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);

    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([

                TextColumn::make('id')
                    ->label('order id')
                    ->searchable(),

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
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('View Order')
                    ->url(fn(Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                    ->color('info')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
