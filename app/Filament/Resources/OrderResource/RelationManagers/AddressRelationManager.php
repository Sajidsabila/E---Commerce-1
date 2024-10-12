<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('firs_name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->required()
                    ->tel()
                    ->maxLength(20),
                TextInput::make('city')
                    ->required()
                    ->maxLength(255),
                TextInput::make('state')
                    ->required()
                    ->maxLength(255),
                TextInput::make('firs_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('zip_code')
                    ->required()
                    ->numeric()
                    ->maxLength(20),
                Forms\Components\TextInput::make('street_address')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('street_address')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('nama customer'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('nomer handphone'),
                Tables\Columns\TextColumn::make('city')
                    ->label('nama kota'),
                Tables\Columns\TextColumn::make('street_address')
                    ->label('kecamatan'),
                Tables\Columns\TextColumn::make('zip_code')
                    ->label('kode pos'),
                Tables\Columns\TextColumn::make('street_address')
                    ->label('alamat lengkap'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
