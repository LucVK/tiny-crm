<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DealResource\Pages;
use App\Filament\Resources\DealResource\RelationManagers;
use App\Filament\Resources\DealResource\RelationManagers\ProductsRelationManager;
use App\Models\Account;
use App\Models\Deal;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DealResource extends Resource
{
    protected static ?string $model = Deal::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        Select::make('customer_id')
                            ->label('Customer')
                            ->options(Account::all()->pluck('name', 'id'))
                            ->searchable(),
                        Select::make('lead_id')
                            ->label('Originating lead')
                            ->options(Lead::all()->pluck('title', 'id'))
                            ->searchable()
                    ])
                    ->columnSpan(2),
                
                    Card::make()
                        ->schema([
                            Select::make('status')
                                ->options([
                                    1 => 'Open',
                                    2 => 'Won',
                                    3 => 'Lost'
                                ]),
                            TextInput::make('estimated_revenue')
                                ->label('Estimated revenue')
                                ->mask(fn (TextInput\Mask $mask) => $mask->money()),
                            TextInput::make('actual_revenue')
                                ->label('Actual revenue')
                                ->mask(fn (TextInput\Mask $mask) => $mask->money())
                        ])
                        ->columnSpan(1)
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->searchable(),
                BadgeColumn::make('status')
                    ->enum([
                        1 => 'Open',
                        2 => 'Won',
                        3 => 'Lost'
                    ])
                    ->colors([
                        'secondary' => 1,
                        'success' => 2,
                        'danger' => 3
                    ])
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        1 => 'Open',
                        2 => 'Won',
                        3 => 'Lost'
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeals::route('/'),
            'create' => Pages\CreateDeal::route('/create'),
            'edit' => Pages\EditDeal::route('/{record}/edit'),
        ];
    }    
}
