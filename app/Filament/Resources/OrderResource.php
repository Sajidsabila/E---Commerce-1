<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use Filament\Forms;
use Filament\Tables;
use App\Models\Order;

use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                            ->label('customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'stripe' => 'Stripe',
                                'cod' => 'Cash On Delevery'
                            ])
                            ->required(),

                        Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'Failed' => 'Failed'
                            ])
                            ->default('panding')
                            ->required(),
                        ToggleButtons::make('status')
                            ->inline()
                            ->options([
                                'new' => 'Pesanan Baru',
                                'processing' => 'Pesanan Di Proses',
                                'shipped' => 'Proses Pengiriman',
                                'delivered' => 'Pesanan Selesai',
                                'cancelled' => 'Pesanan Dibatalkan'
                            ])->colors([
                                    'new' => 'info',
                                    'processing' => 'warning',
                                    'shipped' => 'success',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger'
                                ])
                            ->icons([
                                'new' => 'heroicon-m-sparkles',
                                'processing' => 'heroicon-m-arrow-path',
                                'shipped' => 'heroicon-m-truck',
                                'delivered' => 'heroicon-m-check-badge',
                                'cancelled' => 'heroicon-m-x-circle'
                            ])
                            ->default('new')
                            ->required(),
                        Select::make('shipping_method')
                            ->options([
                                'jne' => 'JNE',
                                'j&t' => 'J&T',
                                'sicepat' => 'SI CEPAT',
                                'tiki' => 'TIKI'
                            ])
                            ->required(),

                        Textarea::make('note')
                            ->columnSpanFull(),


                    ])->columns(2),
                    Section::make('Order Items')->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->columnSpan(4)
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                                    ->afterStateUpdated(fn($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0))
                                // ->currency('IDR') // Opsi tambahan untuk format mata uang Rupiah
                                ,
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->columnSpan(2)
                                    ->minValue(1)
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('total_amount', $state * $get('unit_amount'))),
                                TextInput::make('unit_amount')
                                    ->numeric()
                                    ->required()
                                    ->columnSpan(3)
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('total_amount')
                                    ->numeric()
                                    ->required()
                                    ->columnSpan(3)
                                    ->disabled()
                                    ->dehydrated()

                            ])->columns(12),
                        Placeholder::make('grand_total')
                            ->label('Grand Total')
                            ->content(function (Get $get, Set $set) {
                                $total = 0;
                                if (!$repeaters = $get('items')) {
                                    return $total;
                                }
                                foreach ($repeaters as $key => $data) {
                                    $total += $get("items.{$key}.total_amount");
                                }
                                $set('grand_total', $total);
                                return Number::currency($total, 'Rp. ');
                            }),

                        Hidden::make('grand_total')
                            ->default(0)
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Pemesan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('grand_total')
                    ->label('Total Tagihan')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->money('IDR'),
                TextColumn::make('payment_status')
                    ->label('Status Pembayaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shipping_method')
                    ->label('Jasa Pengiriman')
                    ->sortable()
                    ->searchable(),
                SelectColumn::make('status')
                    ->label('Status Pemesanan')
                    ->options([
                        'new' => 'Pesanan Baru',
                        'processing' => 'Pesanan Di Proses',
                        'shipped' => 'Proses Pengiriman',
                        'delivered' => 'Pesanan Selesai',
                        'cancelled' => 'Pesanan Dibatalkan'
                    ])->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ViewAction::make(),

                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class
        ];
    }
    public static function getNavigationPageBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'danger' : 'success';
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}')
        ];
    }
}
